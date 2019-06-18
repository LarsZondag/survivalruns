<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Run
 *
 * @package App
 * @property int    $id
 * @property string $organiser_name
 * @property string $organiser_url
 * @property string $location
 *
 */
class Run extends Model
{

    protected $protected = [];
    protected $fillable = ['organiser_url'];

    public function runEditions()
    {
        $this->hasMany(RunEdition::class);
    }

    /**
     * @param string $url
     *
     * @return mixed
     * @throws Exception
     */
    static function clean_url(string $url)
    {
        $url_components = parse_url($url);
        $url = $url_components["host"];
        $url_components = explode('.', $url);
        $parts_count = count($url_components);

        return $url_components[$parts_count - 2] . "." . $url_components[$parts_count - 1];
    }

    static function clean_place_name(string $place_name)
    {
        preg_match('/^.*?(?= ONK .SR|$| \()/', $place_name, $matches);

        return $matches[0];
    }
}
