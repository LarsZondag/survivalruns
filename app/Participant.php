<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Participant
 *
 * @package App
 * @property integer $id
 * @property string  $first_name
 * @property string  $last_name
 * @property string  $category
 * @property integer $position
 * @property boolean $DNF
 * @property boolean $DNS
 * @property string  $time
 * @property integer $startnr
 * @property integer $points
 */
class Participant extends Model
{

    protected $guarded = [];

    public function run()
    {
        return $this->belongsTo(Run::class);
    }
}
