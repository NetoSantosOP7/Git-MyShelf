<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/Usuario.php';
require_once __DIR__ . '/../app/Models/Colecao.php';
require_once __DIR__ . '/../app/Models/Livro.php';
require_once __DIR__ . '/../app/Models/Marcador.php';
require_once __DIR__ . '/../app/Helpers/PdfHelper.php';
require_once __DIR__ . '/../app/Helpers/Preferencias.php';
require_once __DIR__ . '/../app/Core/Router.php';


$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

$path = parse_url($requestUri, PHP_URL_PATH);

$baseUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($baseUrl !== '/') {
    $path = str_replace($baseUrl, '', $path);
}

$url = trim($path, '/');

if (empty($url)) {
    $url = '';
}

Router::handle($url);