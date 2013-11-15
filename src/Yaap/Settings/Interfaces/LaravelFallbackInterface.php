<?php namespace Yaap\Settings\Interfaces;


/**
 * Class LaravelFallbackInterface
 * @package Yaap\Settings\Interfaces
 */
class LaravelFallbackInterface implements FallbackInterface {

    /**
     * @param $key
     * @return mixed
     */
    public function fallbackGet($key)
    {
        return \App::make('config')->get($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function fallbackHas($key)
    {
        return \App::make('config')->has($key);
    }
}