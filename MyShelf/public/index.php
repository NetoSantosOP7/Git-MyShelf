<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/Usuario.php';
require_once __DIR__ . '/../app/Models/Colecao.php';
require_once __DIR__ . '/../app/Models/Livro.php';
require_once __DIR__ . '/../app/Helpers/PdfHelper.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Models/Marcador.php';
require_once __DIR__ . '/../app/Helpers/Preferencias.php';

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

$url = str_replace(dirname($scriptName), '', $requestUri);
$url = trim($url, '/');
$url = strtok($url, '?');

if (empty($url)) {
    $url = '';
}

Router::handle($url);