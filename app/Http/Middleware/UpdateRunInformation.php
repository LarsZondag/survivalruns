<?php

namespace App\Http\Middleware;

use App\Organiser;
use App\Run;
use Carbon\Carbon;
use Closure;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;

class UpdateRunInformation
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        libxml_use_internal_errors(true);
        if (!isset($request->year)) {
            throw new Exception('No year was set.');
        }

        if ($run = Run::where('year', $request->year)->first()) {
            if ($run->updated_at->diffInMinutes() <= config("survivalruns.update_time")) {
                return $next($request);
            }
        }

        $url = 'https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/' . $request->year;
        $html = file_get_contents($url);
        $dom = new domDocument;
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $agenda = $dom->getElementById('agenda_content');

        $table = $agenda->getElementsByTagName('table')->item(0);
        $rows = $table->getElementsByTagName('tr');
        $processedHeader = false;

        $promises = [];
        foreach ($rows as $row) {
            if (!$processedHeader) {
                $processedHeader = true;
                continue;
            }

            $columns = $row->getElementsByTagName('td');

            // Update or create the Organiser entry
            $organiser_url = $columns->item(9)->getElementsByTagName('a')->item(0)->getAttribute('href');
            $organiser_url = Organiser::clean_url($organiser_url);

            $organiser_name = $columns->item(9)->textContent;

            $uvp_location = $columns->item(1)->textContent;
            $organiser_location = Organiser::clean_place_name($uvp_location);

            $organiser = Organiser::updateOrCreate(['url' => $organiser_url],
                ['name' => $organiser_name, 'location' => $organiser_location]);

            // Date
            $date = $columns->item(0)->textContent;
            $date = Carbon::parse($date);
            $run = Run::firstOrNew(['organiser_id' => $organiser->id, 'date' => $date]);

            $run->year = $request->year;

            // Circuits
            $run->LSR = $columns->item(2)->textContent == "L";
            $run->MSR = $columns->item(3)->textContent == "M";
            $run->KSR = $columns->item(4)->textContent == "K";
            $run->JSR = $columns->item(5)->textContent == "J";

            // Check if it is an ONK
            if (strripos($uvp_location, "ONK") !== false) {
                $uvp_location = trim($uvp_location, "\xC2\xA0\n");
                $substr_start = strripos($uvp_location, "ONK");
                $category = substr($uvp_location, $substr_start + 4, 3);
                if ($category === "JSR") {
                    $run->ONK_JSR = true;
                } elseif ($category === "KSR") {
                    $run->ONK_KSR = true;
                } elseif ($category === "MSR") {
                    $run->ONK_MSR = true;
                } elseif ($category === "LSR") {
                    $run->ONK_LSR = true;
                }
            }

            // Qualification run
            $run->qualification_run = strpos($columns->item(6)->textContent, "run") !== false;

            // Distances
            $run->distances = $columns->item(7)->textContent;

            // Enrollment
            $run->enrollment_open = $columns->item(10)->textContent === ">schrijf hier in<";

            // uvponline_id
            $run->uvponline_id = $this->get_uvponline_id($columns) ?: $run->uvponline_id;
            $run->uvponline_results_id = $this->get_uvponline_results_id($columns) ?: $run->uvponline_results_id;
            $run->updated_at = Carbon::now();
            $run->save();
            $promises = array_merge($run->updateParticipants(), $promises);
        }
        foreach ($promises as $promise) {
            $promise->wait();
            $promise->resolve(null);
        }
        return $next($request);
    }

    /**
     * @param $columns
     *
     * @return int|null
     * @throws Exception
     */
    private function get_uvponline_id($columns)
    {
        $uvponline_id = null;
        $aElements = $columns->item(10)->getElementsByTagName('a'); // Enrollment
        if ($aElements->count() > 0) {
            $inschrijf_link = $aElements->item(0)->getAttribute('href');
            $link_array = explode("/", $inschrijf_link);
            $uvponline_id = (int)end($link_array);
        }

        return $uvponline_id;
    }

    /**
     * @param $columns
     *
     * @return int|null
     * @throws Exception
     */
    private function get_uvponline_results_id($columns)
    {
        $uvponline_results_id = null;

        $rElements = $columns->item(11)->getElementsByTagName('a'); // Results
        if ($rElements->count() > 0) {
            $results_link = $rElements->item(0)->getAttribute('href');
            $link_array = explode("/", $results_link);
            if (!is_null($uvponline_results_id) && $uvponline_results_id !== (int)end($link_array)) {
                throw new Exception('Multiple uvponline ids found for same run. id1: ' . $uvponline_results_id / " id2: " . end($link_array));
            }

            $uvponline_results_id = (int)end($link_array);
        }

        return $uvponline_results_id;
    }
}
