<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index'); });
Route::post('/operation', 'api_controller@operation');
Route::post('/get_operations', 'api_controller@get_operations');

