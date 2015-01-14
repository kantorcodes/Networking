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
            $shrunk            = str_replace("[","",$data["body"]);
            $shrunk            = str_replace("]","",$shrunk);
            $data["body"]      = stripslashes(substr($shrunk, 0, 2000));
        }

        $this->queue->push(function($job) use ($data)
        {
            $reencoded    = json_encode(["body" => $data["body"],"responseType" => "html"]);
            $data["body"] =  trim($reencoded, '"');

            Request::create($data);
            $job->delete();
        });
    }

}