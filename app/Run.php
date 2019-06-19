<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{

    protected $fillable = ['organiser_id', 'date'];

    protected $dates = ['date'];

    public function organiser()
    {
        return $this->belongsTo(Organiser::class);
    }
}
