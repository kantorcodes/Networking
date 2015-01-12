<?php namespace Drapor\Networking\Laravel\Observers;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/12/15
 * Time: 4:27 PM
 */
use \Illuminate\Events\Dispatcher;
class NetworkObserver{

    protected $events;

    public function __construct(Dispatcher $dispatcher){

        $this->events = $dispatcher;
    }

    public function fire($eventName,array $payload ){
        $this->events->fire($eventName,[$payload]);
    }
}