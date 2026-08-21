<?php

use Illuminate\Http\Request;

// All of the dependencies rely on this file
require __DIR__.'/../vendor/autoload.php';

// Load the application
$app = require_once __DIR__.'/../bootstrap/app.php';

// Handle the request
$request = Request::capture();
$response = $app->handle($request);
$response->send();
$app->terminate($request, $response);
