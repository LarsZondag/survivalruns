<?php

namespace App;

use Carbon\Carbon;
use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

//use GuzzleHttp\Client;

/**
 * Class Run
 *
 * @package App
 *
 * @property integer id
 * @property Carbon  date
 * @property boolean enrollment_open
 * @property Carbon  enrollment_start_date
 * @property boolean LSR
 * @property boolean MSR
 * @property boolean KSR
 * @property boolean JSR
 * @property boolean ONK_LSR
 * @property boolean ONK_MSR
 * @property boolean ONK_KSR
 * @property boolean ONK_JSR
 * @property boolean qualification_run
 * @property integer year
 * @property string  distances
 * @property integer organiser_id
 * @property integer uvponline_id
 * @property integer uvponline_results_id
 * @property Carbon  enrollment_updated
 * @property Carbon  start_times_updated
 * @property Carbon  preliminary_results_updated
 * @property Carbon  results_updated
 */
class Run extends Model
{

    protected $fillable = ['organiser_id', 'date'];

    protected $dates = [
        'date',
        'enrollment_updated',
        'start_times_updated',
        'preliminary_results_updated',
        'results_updated'
    ];

    public function organiser()
    {
        return $this->belongsTo(Organiser::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * @return array
     */
    public function updateParticipants()
    {
        $uvponline_id_set = isset($this->uvponline_id);
        $uvponline_results_id_set = isset($this->uvponline_results_id);
        $promises_array = [];

        // Data depending on $uvponline_id
        if ($uvponline_id_set && !$uvponline_results_id_set) {

            $run_is_in_future = $this->date->isAfter(Carbon::now());
            $enrollment_updated_is_null = is_null($this->enrollment_updated);
            $update_expired = $enrollment_updated_is_null || $this->enrollment_updated->diffInMinutes() > config("survivalruns.update_time");
            $enrollment_not_updated_before_date = !$enrollment_updated_is_null && $this->enrollment_updated->diffInMinutes($this->date) > config("survivalruns.update_time");
            if ($run_is_in_future && $update_expired || $enrollment_not_updated_before_date) {
                $promises_array[] = $this->updateEnrollment();
            }

            if (is_null($this->start_times_updated) || $this->start_times_updated->diffInMinutes() > config("survivalruns.update_time")) {
                $this->updateStartTimes();
            }

            $run_is_now_or_in_past = $this->date->lte(Carbon::now());
            $update_expired = is_null($this->preliminary_results_updated) || $this->preliminary_results_updated->diffInMinutes() > config("survivalruns.update_time");
            if ($run_is_now_or_in_past && $update_expired) {
                // TODO: get preliminary results.
            }
        }

        // Data depending on $uvponline_results_id
        if ($uvponline_results_id_set) {
            if (is_null($this->results_updated)) {
                $promises_array[] = $this->updateResults();
            }
        }

        $this->save();
        return $promises_array;
    }

    private function updateEnrollment()
    {
        $client = new Client(["timeout" => 20,]);
        $url = "https://www.uvponline.nl/uvponlineF/inschrijven_overzicht/" . $this->uvponline_id;
        $promise = $client->getAsync($url)->then(function (Response $response) {
            $html = $response->getBody();
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $enrollment_container = $dom->getElementById('ingeschreven_cats');
            if (is_null($enrollment_container)) {
                Log::notice('Could not retrieve participants for: ' . $this->organiser->name . ' year: ' . $this->year);

                return;
            }
            $category_containers = $enrollment_container->getElementsByTagName('a');
            $category_requests = [];
            $categories = [];
            foreach ($category_containers as $category_container) {
                if (!strpos($category_container->getAttribute('href'), 'inschrijven_overzicht')) {
                    continue;
                }
                $category_requests[] = new Request('GET', $category_container->getAttribute('href'));
                $categories[] = $category_container->textContent;
            }
            $client = new Client(["timeout" => 20,]);
            $pool = new Pool($client, $category_requests, [
                'fulfilled' => function (Response $response, $index) use ($categories) {
                    $this->processEnrollmentPage((string)$response->getBody(), $categories[$index]);
                },
                'rejected'  => function (GuzzleException $reason, $index) {
                    Log::error("Could not GET category enrollments: " . $reason->getMessage());
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();
            $promise->resolve(null);
        });
        $this->enrollment_updated = Carbon::now();

        return $promise;
    }

    private function processEnrollmentPage(string $html, string $category)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $overzicht_indiv = $dom->getElementById('overzicht_indiv');
        if (is_null($overzicht_indiv)) {
            if (!is_null($dom->getElementById("overzicht_groep"))) {
                Log::notice('Skipped enrollment data for team run: ' . $this->organiser->name . ' year: ' . $this->year . " category: " . $category);

                return;
            }
            Log::notice('Could not retrieve participants for: ' . $this->organiser->name . ' year: ' . $this->year . " category: " . $category);

            return;
        }
        $rows = $overzicht_indiv->getElementsByTagName('tr');
        $processed_header = false;
        foreach ($rows as $row) {
            if (!$processed_header) {
                $processed_header = true;
                continue;
            }
            $columns = $row->getElementsByTagName('td');
            if (strcasecmp(trim($columns->item(2)->textContent, "\xC2\xA0\n"), "delft") === 0) {
                $props = [
                    'first_name' => trim($columns->item(1)->textContent, "\xC2\xA0\n"),
                    'last_name'  => trim($columns->item(0)->textContent, "\xC2\xA0\n"),
                    'category'   => trim($category, "\xC2\xA0\n"),
                    'run_id'     => $this->id,
                ];
                Participant::firstOrCreate($props);
            }
        }
    }

    private function updateStartTimes()
    {
        return;
    }

    private function updatePreliminaryResults()
    {
        return;
    }

    private function updateResults()
    {
        $this->participants()->delete();
        $client = new Client(["timeout" => 20,]);
        $url = "https://www.uvponline.nl/uvponlineU/index.php/uitslag/toonuitslag/" . $this->year . "/" . $this->uvponline_results_id;
        $promise = $client->getAsync($url)->then(function (Response $response) {
            $html = $response->getBody();
            $dom = new DOMDocument;
            $dom->loadHTML($html);
            $results_table = $dom->getElementsByTagName('table');
            if ($results_table->count() == 0) {
                Log::notice("No results table found for: " . $this->organiser->name . " " . $this->year);
            }
            $results_table = $results_table->item(0);
            $category_containers = $results_table->getElementsByTagName('tr');
            $category_requests = [];
            $categories = [];
            foreach ($category_containers as $category_container) {
                $category_results = $category_container->getElementsByTagName('td');
                $category = trim($category_results[1]->textContent, "\xC2\xA0\n");
                for ($i = 2; $i < $category_results->count(); $i++) {
                    $url = $category_results[$i]->getElementsByTagName('a');
                    if ($url->count() == 0) {
                        break;
                    }
                    $url = $url->item(0)->getAttribute('href');
                    $category_requests[] = new Request('GET', $url);
                    $categories[] = $category;
                }
            }
            $client = new Client(["timeout" => 20,]);
            $pool = new Pool($client, $category_requests, [
                'fulfilled' => function (Response $response, $index) use ($categories) {
                    $this->processResultsPage((string)$response->getBody(), $categories[$index]);
                },
                'rejected'  => function (GuzzleException $reason, $index) {
                    Log::error("Could not GET category enrollments: " . $reason->getMessage());
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();
            $promise->resolve(null);
        });

        $this->results_updated = Carbon::now();

        return $promise;
    }

    private function processResultsPage(string $html, string $category)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $table = $dom->getElementsByTagName('table');
        if ($table->count() == 0) {
            Log::notice('No results table found for run: ' . $this->organiser->name . " " . $this->year . " category: " . $category);
        }
        $table = $table->item(0);
        $rows = $table->getElementsByTagName('tr');

        // Check if team run, otherwise skip this entry
        $name = trim($rows->item(0)->getElementsbyTagName('td')->item(1)->textContent, "\xC2\xA0\n");
        if (strcasecmp($name, "team") === 0) {
            Log::notice('Skipped results data for team run: ' . $this->organiser->name . ' year: ' . $this->year . " category: " . $category);
        }

        for ($i = 1; $i < $rows->count(); $i++) {
            $row = $rows->item($i)->getElementsByTagName('td');
            if (strcasecmp(trim($row->item(3)->textContent, "\xC2\xA0\n"), "delft") === 0) {
                $props = [
                    'first_name' => trim($row->item(1)->textContent, " \t\n\r\0\v\xC2\xA0\n"),
                    'last_name'  => trim($row->item(2)->textContent, " \t\n\r\0\v\xC2\xA0\n"),
                    'category'   => $category,
                    'startnr'    => (int)trim($row->item(5)->textContent, " \t\n\r\0\v\xC2\xA0\n"),
                    'time'       => trim($row->item(6)->textContent, " \t\n\r\0\v\xC2\xA0\n"),
                    'points'     => (int)str_replace('.', "",
                        trim($row->item(8)->textContent, " \t\n\r\0\v\xC2\xA0\n")),
                    'run_id'     => $this->id,
                ];
                $position = strtoupper(trim($row->item(0)->textContent, " \t\n\r\0\v\xC2\xA0\n"));
                if (ctype_digit($position)) {
                    $props["position"] = $position;
                } elseif ($position == "DNS") {
                    $props["DNS"] = true;
                } elseif ($position == "DNF") {
                    $props["DNF"] = true;
                }
                Participant::firstOrCreate($props);
            }
        }
    }
}
