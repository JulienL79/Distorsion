<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once './core/Autoloader.php';
require_once './core/Utilities.php';

$db = new Database();
$pdo = $db->getConnection();

$router = new Router($pdo);

$router->routeRequest();

?>

