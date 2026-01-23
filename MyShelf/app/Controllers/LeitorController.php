<?php

class LeitorController {
    
    public function index() {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: /livros');
            exit;
        }
        
        $livroModel = new Livro();
        $livro = $livroModel->buscarPorId($id, $_SESSION['usuario_id']);
        
        if (!$livro) {
            $_SESSION['erro'] = 'Livro não encontrado!';
            header('Location: /livros');
            exit;
        }
        
        require_once __DIR__ . '/../Views/leitor/index.php';
    }
    
    public function salvarProgresso() {
        if (!Usuario::estaLogado()) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado']);
            exit;
        }
        
        $json = file_get_contents('php://input');
        $dados = json_decode($json, true);
        
        $livro_id = $dados['livro_id'] ?? null;
        $pagina = $dados['pagina'] ?? null;
        
        if (!$livro_id || !$pagina) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }
        
        $livroModel = new Livro();
        $resultado = $livroModel->atualizarProgresso($livro_id, $_SESSION['usuario_id'], $pagina);
        
        echo json_encode(['success' => $resultado]);
        exit;
    }
}