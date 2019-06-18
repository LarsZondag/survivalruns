<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    libxml_use_internal_errors(true);
    $url = 'https://www.uvponline.nl/uvponlineU/index.php/uvproot/wedstrijdschema/2019';
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
        // Datum
        $datum = $columns->item(0)->textContent;
        echo "Datum: " . $datum . "<br>";

        // Plaats
        $plaats = $columns->item(1)->textContent;
        echo "Plaats: " . $plaats . "<br>";

        // Circuits
        $circuit_LSR = $columns->item(2)->textContent == "L";
        $circuit_MSR = $columns->item(3)->textContent == "M";
        $circuit_KSR = $columns->item(4)->textContent == "K";
        $circuit_JSR = $columns->item(5)->textContent == "J";
        echo "Circuits -> LSR: " . $circuit_LSR . " MSR: " . $circuit_MSR . " KSR: " . $circuit_KSR . " JSR: " . $circuit_JSR . "<br>";

        // Kwalificatie run
        $kwalificatie_run = strpos($columns->item(6)->textContent, "run") !== false;

        echo "Kwalificatie run: " . $kwalificatie_run . "<br>";

        // Afstanden
        $afstanden = $columns->item(7)->textContent;
        echo "Afstanden: " . $afstanden . "<br>";

        // Organisator
        $organisator_naam = $columns->item(9)->textContent;
        $organisator_url = $columns->item(9)->getElementsByTagName('a')->item(0)->getAttribute('href');
        echo "Organisator: " . $organisator_naam . " link: " . $organisator_url . "<br>";

        // Inschrijven
        $inschrijven_beschikbaar = $columns->item(10)->textContent === ">schrijf hier in<";
        $inschrijflink = null;

        $aElements = $columns->item(10)->getElementsByTagName('a');
        if ($aElements->count() > 0) {
            $inschrijflink = $aElements->item(0)->getAttribute('href');
        }
        echo "Inschrijving open: " . $inschrijven_beschikbaar . " inschrijflink: " . $inschrijflink . "<br>";

        echo "<br>";

    }
    //#Get header name of the table

});
