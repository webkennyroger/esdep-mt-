<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);

// Ensure .env exists for Vercel serverless
if (!file_exists($basePath.'/.env')) {
    if (file_exists($basePath.'/.env.production')) {
        copy($basePath.'/.env.production', $basePath.'/.env');
    } elseif (file_exists($basePath.'/.env.example')) {
        copy($basePath.'/.env.example', $basePath.'/.env');
    }
}

// Ensure SQLite database exists
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
