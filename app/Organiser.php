<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organiser extends Model
{

    protected $guarded = [];

    public function runs()
    {
        return $this->hasMany(Run::class);
    }

    static function clean_url(string $url)
    {
        $url_components = parse_url($url);
        $url = $url_components["host"];
        $url_components = explode('.', $url);
        $parts_count = count($url_components);

        return strtolower($url_components[$parts_count - 2] . "." . $url_components[$parts_count - 1]);
    }

    static function clean_place_name(string $place_name)
    {
        preg_match('/^.*?(?= ONK .SR|$| \()/', $place_name, $matches);

        return $matches[0];
    }
}
