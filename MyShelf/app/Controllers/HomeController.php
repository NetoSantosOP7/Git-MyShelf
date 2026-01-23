<?php

class HomeController {
    
    public function index() {
        if (Usuario::estaLogado()) {
            header('Location: /dashboard');
            exit;
        }
        
        require_once __DIR__ . '/../Views/home.php';
    }
}