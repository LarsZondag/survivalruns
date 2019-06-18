<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RunEdition
 *
 * @package App
 * @property int           $id
 * @property int           $uvponline_id
 * @property Carbon        $date
 * @property Carbon | null $enrollment_start_date
 * @property boolean       $LSR
 * @property boolean       $MSR
 * @property boolean       $KSR
 * @property boolean       $JSR
 * @property boolean       $qualification_run
 * @property string        $year
 * @property string        $distances
 */
class RunEdition extends Model
{

    protected $protected = [];
    protected $fillable = ['run_id', 'year'];

    protected $dates = ['date', 'enrollment_start_date'];

    public function run()
    {
        $this->belongsTo(Run::class);
    }
}
