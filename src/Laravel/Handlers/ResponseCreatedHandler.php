<?php namespace Drapor\Networking\Laravel\Handlers;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/9/15
 * Time: 9:59 PM
 */
use Drapor\Networking\Models\Request;
class ResponseCreatedHandler{

    protected $queue;

    public function __construct(){
        $this->queue = app('queue');
    }
    /**
     * @param array $data
     */
    public function handle(array $data){

        if(strlen($data["body"]) >= 2000){
            $data["body"] =  json_encode(str_replace("\\","",substr($data["body"], 0, 2000)));
        }

        $this->queue->push(function($job) use ($data)
        {
            Request::create($data);
            $job->delete();
        });
    }
}