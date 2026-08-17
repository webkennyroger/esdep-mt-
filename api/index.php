<?php

use Illuminate\Http\Request;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

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

try {
    require $basePath.'/vendor/autoload.php';
    $app = require_once $basePath.'/bootstrap/app.php';
    $request = Request::capture();
    $response = $app->handle($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "CAUSA REAL DO ERRO:\n\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}
