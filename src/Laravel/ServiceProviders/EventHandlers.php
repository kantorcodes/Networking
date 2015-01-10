<?php namespace Drapor\Networking\Laravel\ServiceProviders;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/9/15
 * Time: 9:59 PM
 */
use Illuminate\Events\Dispatcher as Dispatcher;
use Drapor\Services\Request;

class EventHandlers{

	protected $request;

	public function __construct(Request $request){
		$this->request = $request;
	}

	/**
	 * @param $events Dispatcher
	 */
	public function subscribe($events)
	{
		$events->listen('response.created', 'Drapor\Networking\Laravel\ServiceProviders\EventHandlers@handleResponseCreated');
	}


	/**
	 * @param array $data
	 */
	public function handleResponseCreated(array $data){

		$this->request->create($data);
	}
}