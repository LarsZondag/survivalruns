<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{

    protected $guarded = [];

    public function run()
    {
        return $this->belongsTo(Run::class);
    }
}
