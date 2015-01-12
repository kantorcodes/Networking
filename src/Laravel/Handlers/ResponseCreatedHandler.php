<?php namespace Drapor\Networking\Laravel\Handlers;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/9/15
 * Time: 9:59 PM
 */
use Illuminate\Events\Dispatcher as Dispatcher;
use Drapor\Networking\Models\Request;

class ResponseCreatedHandler{


    /**
     * @param $events Dispatcher
     */
    public function subscribe($events)
    {
        \Log::info("Subscribing...");
        $events->listen('response.created', 'Drapor\Networking\Laravel\ServiceProviders\EventHandlers@handleResponseCreated');
    }


    /**
     * @param array $data
     */
    public function handleResponseCreated($data){

        \Log::info("New Event");
        Request::create($data);
    }
}