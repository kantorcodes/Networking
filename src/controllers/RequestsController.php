<?php
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/11/15
 * Time: 2:53 PM
 */

use Drapor\Networking\Models\Request;

class RequestsController extends Controller{


    protected $request;

    public function __construct(Request $request){
        $this->request = $request;
    }


    /**
     *
     */
    public function index(){
         $requests = $this->request->paginate(20);

        $view['requests'] = $requests;

        return View::make('logs.index');
    }
}