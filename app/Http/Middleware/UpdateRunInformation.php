<?php

namespace App\Http\Middleware;

use App\Run;
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
        $url = 'https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/' . $request->year;
        $html = file_get_contents($url);
        $dom = new domDocument;
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $agenda = $dom->getElementById('agenda_content');

        $table = $agenda->getElementsByTagName('table')->item(0);
        $rows = $table->getElementsByTagName('tr');
        $header = $rows->item(0);
        $processedHeader = false;
        foreach ($rows as $row) {
            if (!$processedHeader) {
                $processedHeader = true;
                continue;
            }

            $columns = $row->getElementsByTagName('td');

            // Update or create the Run entry
            $organisator_url = $columns->item(9)->getElementsByTagName('a')->item(0)->getAttribute('href');
            $organisator_url = Run::clean_url($organisator_url);
            $run = Run::firstOrNew(['organiser_url' => $organisator_url]);

            // Name
            $organisator_naam = $columns->item(9)->textContent;
            $run->organiser_name = $organisator_naam;

            // Place
            $plaats = Run::clean_place_name($columns->item(1)->textContent);
            $run->location = $plaats;
            $run->save();

            // Update or create the RunEdition entry

            // Datum
            $datum = $columns->item(0)->textContent;

            // Circuits
            $circuit_LSR = $columns->item(2)->textContent == "L";
            $circuit_MSR = $columns->item(3)->textContent == "M";
            $circuit_KSR = $columns->item(4)->textContent == "K";
            $circuit_JSR = $columns->item(5)->textContent == "J";

            // Kwalificatie run
            $kwalificatie_run = strpos($columns->item(6)->textContent, "run") !== false;

            // Afstanden
            $afstanden = $columns->item(7)->textContent;

            // Inschrijven
            $inschrijven_beschikbaar = $columns->item(10)->textContent === ">schrijf hier in<";
            $inschrijf_link = null;

            echo "<br>";

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
        $aElements = $columns->item(10)->getElementsByTagName('a');
        if ($aElements->count() > 0) {
            $inschrijf_link = $aElements->item(0)->getAttribute('href');
            $link_array = explode("/", $inschrijf_link);
            $uvponline_id = (int)end($link_array);
        }

        $rElements = $columns->item(10)->getElementsByTagName('a');
        if ($rElements->count() > 0) {
            $results_link = $rElements->item(0)->getAttribute('href');
            $link_array = explode("/", $results_link);
            if (!is_null($uvponline_id) && $uvponline_id !== (int)end($link_array)) {
                throw new Exception('Multiple uvponline ids found for same run. id1: ' . $uvponline_id / " id2: " . end($link_array));
            }

            $uvponline_id = (int)end($link_array);
        }

        return $uvponline_id;
    }
}
