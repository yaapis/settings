<?php namespace Yaap\Settings;

use \Illuminate\Support\Facades\DB;
use Yaap\Settings\Exceptions\SaveError;
/*
 *
 * Example syntax:
 * use Settings (If you are using namespaces)
 *
 * Single dimension
 * set:         Settings::set('name', 'YAAP')
 * get:         Settings::get('name')
 * forget:      Settings::forget('name')
 * has:         Settings::has('name')
 *
 * Multi dimensional
 * set:         Settings::set('names' , array('nick' => 'YAAP', 'email' => 'yaapis@gmail.com'))
 * setArray:    Settings::setArray(array('nick' => 'YAAP', 'email' => 'yaapis@gmail.com'))
 * get:         Settings::get('names.nick')
 * forget:      Settings::forget('names.email'))
 * has:         Settings::has('names.nick')
 *
 * Clear:
 * clear:        Settings::clear()
 */

/**
 * Class Settings
 * @package Yaap\Settings
 *
 * @todo use insert_with_update method after its being implemented
 * http://laravel.uservoice.com/forums/175973-laravel-4/suggestions/3535821-provide-support-for-bulk-insert-with-update-such-
 *
 */
class Settings {

    /**
     * The database table used to store the settings
     * @var string
     */
    protected $table;

    /**
     * The class working array
     * @var array
     */
    protected $settings;


	/**
	 * @param string $table
	 * @param interfaces\FallbackInterface $fallback
	 *
	 * @throws \RuntimeException
	 */
	public function __construct($table,  Interfaces\FallbackInterface $fallback = null)
    {
        $this->table    = $table;
        $this->fallback = $fallback;

	    $this->driver   = DB::getDriverName();

	    if($this->driver  !== 'mysql' ) {
		    throw new \RuntimeException("Database driver {$this->driver} is not supported yet.");
	    }


        // Load the settings and store the contents in $this->settings
        $this->load();
    }



    /**
     * Get a value and return it
     * @param  string $searchKey    String using dot notation
     * @return Mixed                The value(s) found
     */

	/**
	 * Get a value and return it
	 * @param null $searchKey       String using dot notation, NULL value will return all settings list
	 * @param null $d               Default value
	 * @return array|mixed|null
	 */
	public function get($searchKey = null, $d = null)
    {
        if ($searchKey === null)
        {
            return $this->settings;
        }

        $default = microtime(true);

        if($default != array_get($this->settings, $searchKey, $default))
        {
            return array_get($this->settings, $searchKey);
        }

        if ( ! is_null($this->fallback) and $this->fallback->fallbackHas($searchKey))
        {
            $r =  $this->fallback->fallbackGet($searchKey);
	        if (strpos($searchKey, '.') === false && $r === array())
		        return $d;
	        return $r;
        }

        return null;
    }

     /**
     * Put the passed value in to Settings, but not save him
     * @param $key
     * @param  mixed $value         The value(s) to be stored
     * @return bool
     */
    public function put($key, $value)
    {
        return array_set($this->settings,$key,$value);
    }

	/**
     * Set the passed value in to Settings, but not save to table
     * @param $key
     * @param  mixed $value         The value(s) to be stored
     * @return void
	 *
	 * @throws SaveError
     */
    public function set($key, $value)
    {
        array_set($this->settings,$key,$value);

	    try {

		    DB::statement('INSERT INTO  `'.$this->table.'` SET `key`=?, `value`=? ON DUPLICATE KEY UPDATE `value`=?', array($key,$value,$value));

	    } catch (\Exception $e) {

		    throw new SaveError("Cannot save to database: " . $e->getMessage());

		}
    }

    /**
     * Forget the value(s) currently stored
     * @param  mixed $deleteKey         The value(s) to be removed (dot notation)
     * @return void
     */
    public function forget($deleteKey)
    {
        array_forget($this->settings,$deleteKey);
    }

	/**
     * Remove the value(s) currently stored from Settings and DB
     * @param  mixed $deleteKey         The value(s) to be removed (dot notation)
     * @return void
	 *
	 * @throws SaveError
     */
    public function remove($deleteKey)
    {
        array_forget($this->settings,$deleteKey);

	    try {

            DB::statement('DELETE FROM `'.$this->table.'` WHERE `key`=? LIMIT 1', array($deleteKey));

        } catch (\Exception $e) {

            throw new SaveError("Cannot save to database: " . $e->getMessage());

        }
    }

    /**
     * Check to see if the value exists
     * @param  string  $searchKey   The key to search for
     * @return boolean              True: found - False not found
     */
    public function has($searchKey)
    {
        $default = microtime(true);

        if($default == array_get($this->settings, $searchKey, $default) and !is_null($this->fallback))
        {
            return $this->fallback->fallbackHas($searchKey);
        }
        return $default != array_get($this->settings, $searchKey, $default);
    }

    /**
     * Load settings in to $this->settings
     * @return \Yaap\Settings\Settings
     */
    public function load()
    {
       //load from DB $this->table;
	    $list = DB::table($this->table)->lists('value','key');

	    $this->settings = array();

	    // convert dotted list back to multidimensional array
	    foreach ($list as $key => $value) {
          array_set($this->settings, $key, $value);
        }

        return $this;
    }

    /**
     * Save settings
     * @return void
     */
    public function save()
    {

	    $settings = $this->settings;
	    $table = $this->table;


	    DB::transaction(function() use ($settings, $table)
	    {
		   $list = array();

		    // convert multidimensional array into a single level
		    $settings = array_dot($settings);

            foreach ($settings as $key => $value)
                $list[] = array( 'key' => $key, 'value' => $value);

		    try {

			    // delete all previous settings
                DB::table($table)->delete();

			    // reinsert them again
			    if (!empty($list)) DB::table($table)->insert($list);

            } catch (\Exception $e) {

                throw new SaveError("Cannot save to database: " . $e->getMessage());

            }

	    });

    }

    /**
     * Clears settings
     */
    public function clear()
    {
        $this->settings = array();
        $this->save();
    }

	/**
     * This will mass assign data to the Settings
     * @param array $data
     */
    public function putArray(array $data)
    {
        foreach ($data as $key => $value)
        {
            array_set($this->settings,$key,$value);
        }

    }


    /**
     * This will mass assign data to the Settings and save it to DB
     * @param array $data
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value)
        {
            array_set($this->settings,$key,$value);
        }

        $this->save();
    }
}