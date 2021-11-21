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

Route::get('/service', function (Request $request) {

    // Microservice A needs to request a new token using the client-credentials flow, and use that to authenticate with Microservice D

    $customScope = 'machine-scope'; // this is the scope we created in Okta for our default auth server

    // the details for our machine-to-machine application integration
    $clientId = env('OKTA_CLIENT_ID');
    $secret = env('OKTA_SECRET');

    $oktaDomain = env('OKTA_DOMAIN');

    $tokenResponse = \Illuminate\Support\Facades\Http::withBasicAuth($clientId, $secret)
        ->asForm()
        ->post("$oktaDomain/oauth2/default/v1/token", [
            'grant_type' => 'client_credentials',
            'scope' => $customScope
        ]);

    $token = $tokenResponse->json('access_token');

    return \Illuminate\Support\Facades\Http::withToken($token)->get('http://microservice-d/api/service');
});
