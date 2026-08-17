<?php

use Illuminate\Http\Request;

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);

if (!file_exists($basePath.'/.env')) {
    if (file_exists($basePath.'/.env.production')) {
        copy($basePath.'/.env.production', $basePath.'/.env');
    } elseif (file_exists($basePath.'/.env.example')) {
        copy($basePath.'/.env.example', $basePath.'/.env');
    }
}

$dbPath = $basePath.'/database/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}

require $basePath.'/vendor/autoload.php';

$app = require_once $basePath.'/bootstrap/app.php';

$request = Request::capture();
$response = $app->handle($request);
$response->send();
$app->terminate($request, $response);
