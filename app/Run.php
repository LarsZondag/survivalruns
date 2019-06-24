<?php

namespace App;

use Carbon\Carbon;
use DOMDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public function updateParticipants()
    {
        if (!isset($this->uvponline_id)) {
            return;
        }

        if (is_null($this->enrollment_updated) || $this->enrollment_updated->diffInMinutes() > config("survivalruns.update_time")) {
            $this->updateEnrollment();
        }
        if (is_null($this->start_times_updated) || $this->start_times_updated->diffInMinutes() > config("survivalruns.update_time")) {
            $this->updateStartTimes();
        }
        if (is_null($this->preliminary_results_updated) || $this->preliminary_results_updated->diffInMinutes() > config("survivalruns.update_time")) {

        }
        if (is_null($this->results_updated) || $this->results_updated->diffInMinutes() > config("survivalruns.update_time")) {

        }

        $this->save();
    }

    private function updateEnrollment()
    {
        $url = "https://www.uvponline.nl/uvponlineF/inschrijven_overzicht/" . $this->uvponline_id;
        $html = file_get_contents($url);
        $dom = new DOMDocument;
        $dom->loadHTML($html);
        $enrollment_container = $dom->getElementById('ingeschreven_cats');
        if (is_null($enrollment_container)) {
            Log::notice('Could not retrieve participants for: ' . $this->organiser->name . ' year: ' . $this->year . " url: " . $url);

            return;
        }
        $category_containers = $enrollment_container->getElementsByTagName('a');
        foreach ($category_containers as $category_container) {
            if (!strpos($category_container->getAttribute('href'), 'inschrijven_overzicht')) {
                continue;
            }
            $this->processEnrollmentPage($category_container->getAttribute('href'), $category_container->textContent);
        }

        $this->enrollment_updated = Carbon::now();
    }

    private function processEnrollmentPage(string $url, string $category)
    {
        $html = file_get_contents($url);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $overzicht_indiv = $dom->getElementById('overzicht_indiv');
        if (is_null($overzicht_indiv)) {
            Log::notice('Could not retrieve participants for: ' . $this->organiser->name . ' year: ' . $this->year . " url: " . $url);

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
        return;
    }

}
