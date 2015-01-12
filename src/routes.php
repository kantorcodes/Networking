<?php
/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 1/12/15
 * Time: 1:55 PM
 */

Route::get('/networking/requests','Drapor\Networking\Controllers\RequestsController@index');
Route::post('/networking/requests','Drapor\Networking\Controllers\RequestsController@index');