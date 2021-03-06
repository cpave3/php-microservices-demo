<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/service1', function(Request $request) {
    $response = \Illuminate\Support\Facades\Http::get('http://microservice-a/api/service');
    return new \Illuminate\Http\Response($response->body(), $response->status());
});

Route::get('/service2', function(Request $request) {
    $bearer = $request->bearerToken();
    $response = \Illuminate\Support\Facades\Http::withToken($bearer)->get('http://microservice-b/api/service');
    return new \Illuminate\Http\Response($response->body(), $response->status());
});

Route::get('/service3', function(Request $request) {
    $bearer = $request->bearerToken();
    $response = \Illuminate\Support\Facades\Http::withToken($bearer)->get('http://microservice-c/api/service');
    return new \Illuminate\Http\Response($response->body(), $response->status());
});
