<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;
use App\Organiser;
use App\Run;

class updateRunInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runs:updateSeason';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This updates all the runs for the current season';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $current_season = $now->year;
        if ($now->month < 9) {
            $current_season -= 1;
        }

        $url = 'https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/' . $current_season;
        $html = file_get_contents($url);

        $crawler = new Crawler($html);

        $runs = $crawler->filterXPath('//tr[contains(@class, "tbl")]')->each(function(Crawler $node, $i) use ($current_season) {
            $columns = $node->children();

            // Retrieve organiser's information
            $organiser_url = Organiser::clean_url($columns->eq(9)->children()->first()->attr('href'));
            $organiser_name = $columns->eq(9)->text();
            $organiser_location = Organiser::clean_place_name($columns->eq(1)->text());

            // update or create the organiser with current information
            $organiser = Organiser::updateOrCreate(['url' => $organiser_url],
            ['name' => $organiser_name, 'location' => $organiser_location]);
            
            // Now that the organiser is known, retrieve the run information
            $date = Carbon::parse($columns->eq(0)->text());

            $run = [
                "year" => $current_season,
                "LSR" => $columns->eq(2)->text() == "L",
                "MSR" => $columns->eq(3)->text() == "M",
                "KSR" => $columns->eq(4)->text() == "K",
                "JSR" => $columns->eq(5)->text() == "J",
                "qualification_run" => strpos($columns->eq(6)->text(), "run") !== false,
                "distances" => $columns->eq(7)->text(),
            ];
            
            // Set the uvponline_id if it can be retrieved.
            if ($uvponline_id_column = $columns->eq(10)->filter('a')->getNode(0)) {
                $enrollment_link = $uvponline_id_column->getAttribute('href');
                $link_array = explode("/", $enrollment_link);
                $run["uvponline_id"] = (int)end($link_array);
            }

            // Set the uvponline_results_id if it can be retrieved.
            if ($uvponline_results_id_column = $columns->eq(11)->filter('a')->getNode(0)) {
                $results_link = $uvponline_results_id_column->getAttribute('href');
                $link_array = explode("/", $results_link);
                $run["uvponline_results_id"] = (int)end($link_array);
            }

            // Check if it is an ONK
            if (strripos($columns->eq(1)->text(), "ONK") !== false) {
                $uvp_location = trim($columns->eq(1)->text(), "\xC2\xA0\n");
                $substr_start = strripos($uvp_location, "ONK");
                $category = substr($uvp_location, $substr_start + 4, 3);
                
                $run["ONK_JSR"] = $category === "JSR";
                $run["ONK_KSR"] = $category === "KSR";
                $run["ONK_MSR"] = $category === "MSR";
                $run["ONK_LSR"] = $category === "LSR";
                
            }
            $run = Run::updateOrCreate(['organiser_id' => $organiser->id, 'date' => $date], $run);
            return $run;
        });

        foreach ($runs as $run) {
            $run->updateParticipants();
        }
    }
}
