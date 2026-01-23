<?php

class DashboardController {
    
    public function index() {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }
        
        $livroModel = new Livro();
        $colecaoModel = new Colecao();
        
        $totalLivros = $livroModel->contarPorUsuario($_SESSION['usuario_id']);
        $totalColecoes = $colecaoModel->contarPorUsuario($_SESSION['usuario_id']);
        
        $lendo = $livroModel->contarPorStatus($_SESSION['usuario_id'], 'lendo');
        $concluidos = $livroModel->contarPorStatus($_SESSION['usuario_id'], 'concluido');
        $querLer = $livroModel->contarPorStatus($_SESSION['usuario_id'], 'quer_ler');
        
        $ultimosLidos = $livroModel->ultimosLidos($_SESSION['usuario_id'], 3);
        
        $livrosLendo = $livroModel->livrosLendo($_SESSION['usuario_id'], 4);
        
        $ultimasColecoes = $colecaoModel->listarPorUsuario($_SESSION['usuario_id']);
        $ultimasColecoes = array_slice($ultimasColecoes, 0, 3);
        
        require_once __DIR__ . '/../Views/dashboard.php';
    }
}