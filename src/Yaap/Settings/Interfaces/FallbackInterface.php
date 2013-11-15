<?php namespace Yaap\Settings\Interfaces ;

/**
 * Class FallbackInterface
 * @package Yaap\Settings\Interfaces
 */
interface FallbackInterface {

    /**
     * @param $key
     * @return mixed
     */
    public function fallbackGet($key);

    /**
     * @param $key
     * @return boolean
     */
    public function fallbackHas($key);

}