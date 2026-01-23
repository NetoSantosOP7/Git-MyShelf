<?php

class LivroController
{

    public function index()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $livro = new Livro();

        $filtro = $_GET['filtro'] ?? 'todos';

        if ($filtro === 'todos') {
            $livros = $livro->listarPorUsuario($_SESSION['usuario_id']);
        } else {
            $livros = $livro->listarPorStatus($_SESSION['usuario_id'], $filtro);
        }

        require_once __DIR__ . '/../Views/livros/index.php';
    }

    public function upload()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $colecao = new Colecao();
        $colecoes = $colecao->listarPorUsuario($_SESSION['usuario_id']);

        require_once __DIR__ . '/../Views/livros/upload.php';
    }

    public function store()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /livros/upload');
            exit;
        }

        if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['erro'] = 'Erro ao fazer upload do arquivo!';
            header('Location: /livros/upload');
            exit;
        }

        $arquivo = $_FILES['pdf'];

        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if ($extensao !== 'pdf') {
            $_SESSION['erro'] = 'Apenas arquivos PDF são permitidos!';
            header('Location: /livros/upload');
            exit;
        }

        $tamanhoMax = 50 * 1024 * 1024;
        if ($arquivo['size'] > $tamanhoMax) {
            $_SESSION['erro'] = 'O arquivo deve ter no máximo 50MB!';
            header('Location: /livros/upload');
            exit;
        }

        $nomeUnico = uniqid() . '_' . time() . '.pdf';
        $caminhoUpload = __DIR__ . '/../../public/uploads/pdfs/';
        $caminhoCompleto = $caminhoUpload . $nomeUnico;

        if (!is_dir($caminhoUpload)) {
            mkdir($caminhoUpload, 0777, true);
        }

        if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
            $_SESSION['erro'] = 'Erro ao salvar o arquivo!';
            header('Location: /livros/upload');
            exit;
        }

        $infos = PdfHelper::extrairInfos($caminhoCompleto);

        $titulo = $_POST['titulo'] ?? $infos['titulo'] ?? 'Sem título';
        $autor = $_POST['autor'] ?? $infos['autor'] ?? null;
        $descricao = $_POST['descricao'] ?? null;
        $colecao_id = $_POST['colecao_id'] ?? null;
        $status = $_POST['status'] ?? 'quer_ler';
        $tags = $_POST['tags'] ?? null;

        if (empty($titulo)) {
            $titulo = $infos['titulo'] ?: pathinfo($arquivo['name'], PATHINFO_FILENAME);
        }

        $nomeCapa = uniqid() . '_capa.jpg';
        $caminhoCapa = __DIR__ . '/../../public/uploads/capas/';
        $capaCompleta = $caminhoCapa . $nomeCapa;

        if (!is_dir($caminhoCapa)) {
            mkdir($caminhoCapa, 0777, true);
        }

        $capaGerada = false;

        if (isset($_FILES['capa_personalizada']) && $_FILES['capa_personalizada']['error'] === UPLOAD_ERR_OK) {
            $capaFile = $_FILES['capa_personalizada'];

            $imagemInfo = getimagesize($capaFile['tmp_name']);
            if ($imagemInfo !== false) {
                list($larguraOriginal, $alturaOriginal) = $imagemInfo;
                $tipo = $imagemInfo[2];

                switch ($tipo) {
                    case IMAGETYPE_JPEG:
                        $imagemOriginal = imagecreatefromjpeg($capaFile['tmp_name']);
                        break;
                    case IMAGETYPE_PNG:
                        $imagemOriginal = imagecreatefrompng($capaFile['tmp_name']);
                        break;
                    default:
                        $imagemOriginal = false;
                }

                if ($imagemOriginal) {
                    $novaImagem = imagecreatetruecolor(300, 400);
                    imagecopyresampled(
                        $novaImagem,
                        $imagemOriginal,
                        0,
                        0,
                        0,
                        0,
                        300,
                        400,
                        $larguraOriginal,
                        $alturaOriginal
                    );

                    imagejpeg($novaImagem, $capaCompleta, 90);
                    $capaGerada = true;
                }
            }
        }

        if (!$capaGerada && isset($_POST['capa_gerada_base64']) && !empty($_POST['capa_gerada_base64'])) {
            $base64 = $_POST['capa_gerada_base64'];

            if (strpos($base64, 'data:image') === 0) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
            }

            $imagemData = base64_decode($base64);
            if ($imagemData !== false) {
                file_put_contents($capaCompleta, $imagemData);
                $capaGerada = true;
            }
        }

        if (!$capaGerada) {
            $cor = null;
            if ($colecao_id) {
                $colecaoModel = new Colecao();
                $colecaoData = $colecaoModel->buscarPorId($colecao_id, $_SESSION['usuario_id']);
                $cor = $colecaoData['cor'] ?? null;
            }

            PdfHelper::gerarCapa($titulo, $capaCompleta, $cor);
        }

        $livroModel = new Livro();
        $resultado = $livroModel->criar([
            'usuario_id' => $_SESSION['usuario_id'],
            'colecao_id' => $colecao_id ?: null,
            'titulo' => $titulo,
            'autor' => $autor,
            'descricao' => $descricao,
            'arquivo_pdf' => 'uploads/pdfs/' . $nomeUnico,
            'capa_path' => 'uploads/capas/' . $nomeCapa,
            'total_paginas' => $infos['total_paginas'],
            'tamanho_mb' => $infos['tamanho_mb'],
            'status' => $status,
            'tags' => $tags
        ]);

        if ($resultado['success']) {
            $_SESSION['sucesso'] = $resultado['message'];
            header('Location: /livros');
        } else {
            $_SESSION['erro'] = $resultado['message'];
            header('Location: /livros/upload');
        }
        exit;
    }

    public function detalhes()
    {
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

        require_once __DIR__ . '/../Views/livros/detalhes.php';
    }

    public function delete()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /livros');
            exit;
        }

        $livro = new Livro();
        $resultado = $livro->deletar($id, $_SESSION['usuario_id']);

        $_SESSION[$resultado['success'] ? 'sucesso' : 'erro'] = $resultado['message'];
        header('Location: /livros');
        exit;
    }

    public function edit()
    {
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

        $colecaoModel = new Colecao();
        $colecoes = $colecaoModel->listarPorUsuario($_SESSION['usuario_id']);

        require_once __DIR__ . '/../Views/livros/edit.php';
    }

    public function update()
    {
        if (!Usuario::estaLogado()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /livros');
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['erro'] = 'ID do livro não informado!';
            header('Location: /livros');
            exit;
        }

        $titulo = $_POST['titulo'] ?? '';
        $autor = $_POST['autor'] ?? null;
        $descricao = $_POST['descricao'] ?? null;
        $colecao_id = $_POST['colecao_id'] ?? null;
        $status = $_POST['status'] ?? 'quer_ler';
        $tags = $_POST['tags'] ?? null;

        if (empty($titulo)) {
            $_SESSION['erro'] = 'O título é obrigatório!';
            header("Location: /livros/editar?id=$id");
            exit;
        }

        $livroModel = new Livro();
        $resultado = $livroModel->atualizar($id, $_SESSION['usuario_id'], [
            'titulo' => $titulo,
            'autor' => $autor,
            'descricao' => $descricao,
            'colecao_id' => $colecao_id ?: null,
            'status' => $status,
            'tags' => $tags
        ]);

        $_SESSION[$resultado['success'] ? 'sucesso' : 'erro'] = $resultado['message'];
        header('Location: /livros/detalhes?id=' . $id);
        exit;
    }
}