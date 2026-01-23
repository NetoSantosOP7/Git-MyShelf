<?php

class ColecaoController
{

    public function index()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $colecao = new Colecao();
        $colecoes = $colecao->listarPorUsuario($_SESSION['usuario_id']);

        require_once __DIR__ . '/../Views/colecoes/index.php';
    }

    public function ver()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /colecoes');
            exit;
        }

        $colecaoModel = new Colecao();
        $colecao = $colecaoModel->buscarPorId($id, $_SESSION['usuario_id']);

        if (!$colecao) {
            $_SESSION['erro'] = 'Coleção não encontrada!';
            header('Location: /colecoes');
            exit;
        }

        $filtro = $_GET['filtro'] ?? 'todos';

        $livroModel = new Livro();

        if ($filtro === 'todos') {
            $livros = $livroModel->listarPorColecao($id, $_SESSION['usuario_id']);
        } else {
            $livros = $livroModel->listarPorColecaoEStatus($id, $_SESSION['usuario_id'], $filtro);
        }

        require_once __DIR__ . '/../Views/colecoes/ver.php';
    }

    public function create()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        require_once __DIR__ . '/../Views/colecoes/create.php';
    }

    public function store()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $tipo = $_POST['tipo'] ?? 'livros';
            $cor = $_POST['cor'] ?? '#3B82F6';
            $icone = $_POST['icone'] ?? 'book';

            if (empty($nome)) {
                $_SESSION['erro'] = 'O nome da coleção é obrigatório!';
                header('Location: /colecoes/criar');
                exit;
            }

            $colecao = new Colecao();
            $resultado = $colecao->criar([
                'usuario_id' => $_SESSION['usuario_id'],
                'nome' => $nome,
                'descricao' => $descricao,
                'tipo' => $tipo,
                'cor' => $cor,
                'icone' => $icone
            ]);

            if ($resultado['success']) {
                $_SESSION['sucesso'] = $resultado['message'];
                header('Location: /colecoes');
            } else {
                $_SESSION['erro'] = $resultado['message'];
                header('Location: /colecoes/criar');
            }
            exit;
        }
    }

    public function edit()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /colecoes');
            exit;
        }

        $colecaoModel = new Colecao();
        $colecao = $colecaoModel->buscarPorId($id, $_SESSION['usuario_id']);

        if (!$colecao) {
            $_SESSION['erro'] = 'Coleção não encontrada!';
            header('Location: /colecoes');
            exit;
        }

        require_once __DIR__ . '/../Views/colecoes/edit.php';
    }

    public function update()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nome = $_POST['nome'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $tipo = $_POST['tipo'] ?? 'livros';
            $cor = $_POST['cor'] ?? '#3B82F6';
            $icone = $_POST['icone'] ?? 'book';

            if (!$id || empty($nome)) {
                $_SESSION['erro'] = 'Dados inválidos!';
                header('Location: /colecoes');
                exit;
            }

            $colecao = new Colecao();
            $resultado = $colecao->atualizar($id, $_SESSION['usuario_id'], [
                'nome' => $nome,
                'descricao' => $descricao,
                'tipo' => $tipo,
                'cor' => $cor,
                'icone' => $icone
            ]);

            $_SESSION[$resultado['success'] ? 'sucesso' : 'erro'] = $resultado['message'];
            header('Location: /colecoes');
            exit;
        }
    }

    public function delete()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /colecoes');
            exit;
        }

        $colecao = new Colecao();
        $resultado = $colecao->deletar($id, $_SESSION['usuario_id']);

        $_SESSION[$resultado['success'] ? 'sucesso' : 'erro'] = $resultado['message'];
        header('Location: /colecoes');
        exit;
    }
}