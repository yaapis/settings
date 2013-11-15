<?php namespace Yaap\Settings;

use Illuminate\Support\ServiceProvider;
use Yaap\Settings\Interfaces\LaravelFallbackInterface;
use \Illuminate\Support\Facades\DB;

class SettingsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('yaap/settings');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app['settings'] = $this->app->share(function($app)
        {
	        $table = $app['config']['settings::table'];

			return new Settings($table, $app['config']['settings::fallback'] ? new LaravelFallbackInterface() : null);
        });

		$this->app->booting(function()
        {
          $loader = \Illuminate\Foundation\AliasLoader::getInstance();
          $loader->alias('Settings', 'Yaap\Settings\Facades\Settings');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('settings');
	}

}