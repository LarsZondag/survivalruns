<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ["first_name", "last_name"];

    public function getFullNameAttribute() {
        return $this->first_name . " " . $this->last_name;
    }

    public function runs() {
        return $this->belongsToMany('App\Run');
    }
}
