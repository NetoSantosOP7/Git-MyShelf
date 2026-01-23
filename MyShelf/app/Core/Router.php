<?php

class Router {
    
    public static function handle($url) {
    
        $routes = require_once __DIR__ . '/../../routes/web.php';
        
        $url = trim($url, '/');
        
        if (!array_key_exists($url, $routes)) {
            self::notFound();
            return;
        }
        
      
        list($controllerName, $method) = explode('@', $routes[$url]);
        
        $controllerPath = __DIR__ . '/../Controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerPath)) {
            die("Controller não encontrado: {$controllerName}");
        }
        
        require_once $controllerPath;
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $method)) {
            die("Método {$method} não encontrado em {$controllerName}");
        }
        
        $controller->$method();
    }
    
    private static function notFound() {
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        exit;
    }
}