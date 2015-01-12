<?php namespace Drapor\Networking;

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
		$this->package('drapor/networking', 'drapor/networking', __DIR__.'/..');
        \View::addNamespace('networking', __DIR__.'/views');
        include __DIR__.'/routes.php';

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//$this->app->register('Drapor\Networking\Laravel\ServiceProviders\EventHandlerProvider');
        $this->app->bind('Drapor\Networking\Laravel\ServiceProviders\EventHandlerProvider');
        $this->registerModels();
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


    public function registerModels()
    {
        $this->app['requestsModel'] = $this->app->share(function () {
            return new \Drapor\Networking\Models\Request();
        });

    }
}
