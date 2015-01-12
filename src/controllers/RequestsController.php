<?php namespace Drapor\Networking\Controllers;
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/11/15
 * Time: 2:53 PM
 */

use Drapor\Networking\Models\Request;
use \Illuminate\Routing\Controller;
use View;

class RequestsController extends Controller{


    protected $request;
    protected $view;

    public function __construct(Request $request, View $view){
        $this->request = $request;
        $this->view    = $view;
    }

    public function index(){
        $requests = $this->request->paginate(20);

        $view['requests'] = $requests;

        return View::make('networking::logs.index',$view);
    }
}