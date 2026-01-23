<?php

class MarcadorController {
    
    public function adicionar() {
        if (!Usuario::estaLogado()) {
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit;
        }
        
        $json = file_get_contents('php://input');
        $dados = json_decode($json, true);
        
        $livro_id = $dados['livro_id'] ?? null;
        $pagina = $dados['pagina'] ?? null;
        $titulo = $dados['titulo'] ?? 'Página ' . $pagina;
        
        if (!$livro_id || !$pagina) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }
        
        $livroModel = new Livro();
        $livro = $livroModel->buscarPorId($livro_id, $_SESSION['usuario_id']);
        
        if (!$livro) {
            echo json_encode(['success' => false, 'message' => 'Livro não encontrado']);
            exit;
        }
        
        $marcadorModel = new Marcador();
        $resultado = $marcadorModel->adicionar($livro_id, $pagina, $titulo);
        
        echo json_encode($resultado);
        exit;
    }
    
    public function listar() {
        if (!Usuario::estaLogado()) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $livro_id = $_GET['livro_id'] ?? null;
        
        if (!$livro_id) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $livroModel = new Livro();
        $livro = $livroModel->buscarPorId($livro_id, $_SESSION['usuario_id']);
        
        if (!$livro) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $marcadorModel = new Marcador();
        $marcadores = $marcadorModel->listarPorLivro($livro_id);
        
        echo json_encode(['success' => true, 'marcadores' => $marcadores]);
        exit;
    }
    
    public function deletar() {
        if (!Usuario::estaLogado()) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $json = file_get_contents('php://input');
        $dados = json_decode($json, true);
        
        $id = $dados['id'] ?? null;
        $livro_id = $dados['livro_id'] ?? null;
        
        if (!$id || !$livro_id) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $marcadorModel = new Marcador();
        $resultado = $marcadorModel->deletar($id, $livro_id);
        
        echo json_encode($resultado);
        exit;
    }
}