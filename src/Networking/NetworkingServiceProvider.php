<?php namespace Drapor\Services;

use Illuminate\Support\ServiceProvider;

class NetworkingServiceProvider extends ServiceProvider {

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
		$this->package('drapor/services', 'drapor/services', __DIR__.'/..');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->register('Drapor\Services\Laravel\ServiceProviders\EventHandlerProvider');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
