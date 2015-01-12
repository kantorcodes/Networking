<?php namespace Drapor\Networking\Laravel\Handlers;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/9/15
 * Time: 9:59 PM
 */
use Drapor\Networking\Models\Request;

class ResponseCreatedHandler{


    /**
     * @param array $data
     */
    public function handle(array $data){

        Request::create($data);
    }
}