<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Collection::macro('diffByKeys', function ($keys, Collection $haystack) {
            return $this->filter(function($value) use ($keys, $haystack) {
                $found = false;
                foreach($haystack as $hay) {
                    $is_same = true;
                    foreach ($keys as $key) {
                        if ($value[$key] !== $hay[$key]) {
                            $is_same = false;
                            break;
                        }
                    }
                    if ($is_same) {
                        $found = true;
                        break;
                    }
                }
                return !$found;
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
