<?php

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;


$dotenv = new Dotenv();
$dotenv->usePutenv()->load(__DIR__ . '/.env');
$dbConn = getenv('DB_CONNECTION');
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

return new PDO ("$dbConn:host=$dbHost;dbname=$dbName","$dbUsername","$dbPassword");