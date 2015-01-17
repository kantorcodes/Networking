<?php namespace Drapor\Networking\Laravel\Handlers;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/9/15
 * Time: 9:59 PM
 */
use Drapor\Networking\Models\Request;
use Illuminate\Queue\Queue;
class ResponseCreatedHandler{

    /**
     * @param Queue $data
     */
    protected $queue;

    public function __construct(){
        $this->queue = app('queue');
    }
    /**
     * Rather than do any json encoding in the Networking Class
     * The Handle method will encode all data and the Request model will
     * do any additional checks allowing the user to replace the handle method
     * if they choose to change what happens with a response.
     * @param array $networking
     */
    public function handle(array $networking){

        $stripped  = false;
        dd($networking);

        if($networking["response_type"] == "html/xml"){
            if(strlen($body = $networking["response_body"][0]) >= 2000){
                $stripped = true;
                $networking["response_body"] = serialize(stripslashes(trim(mb_substr($body,0,2000))));
            }
        }

        $this->queue->push(function($job) use ($networking, $stripped)
        {
            if($stripped){
                $networking["response_body"] = unserialize($networking["response_body"]);
            }

            $data = [
                'status_code'           => $networking["status_code"],
                'response_body'         => json_encode($networking["response_body"]),
                'request_body'          => json_encode($networking["request_body"]),
                'url'                   => $networking["url"],
                'response_headers'      => json_encode($networking["response_headers"]),
                'request_headers'       => json_encode($networking["request_headers"]),
                'cookies'               => json_encode($networking["cookies"]),
                'time_elapsed'          => $networking["time_elapsed"],
                'response_type'         => $networking["response_type"],
                'method'                => $networking["method"]
            ];

            Request::create($data);
            $job->delete();
        });
    }


}