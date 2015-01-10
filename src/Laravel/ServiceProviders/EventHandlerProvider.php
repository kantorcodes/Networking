<?php namespace Drapor\Services\Laravel\ServiceProviders;
use Illuminate\Support\ServiceProvider;

/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/9/15
 * Time: 9:55 PM
 */

	class EventHandlerProvider extends ServiceProvider{

		public function boot(){
			$this->app['events']->subscribe('Drapor\Services\Laravel\ServiceProviders\EventHandlers');
		}

		public function register(){

		}

	}