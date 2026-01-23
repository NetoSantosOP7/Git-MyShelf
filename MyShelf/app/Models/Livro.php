<?php

class Livro
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarPorUsuario($usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT l.*, c.nome as colecao_nome, c.cor as colecao_cor 
                FROM livros l
                LEFT JOIN colecoes c ON l.colecao_id = c.id
                WHERE l.usuario_id = ?
                ORDER BY l.created_at DESC
            ");
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarPorColecao($colecao_id, $usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM livros 
                WHERE colecao_id = ? AND usuario_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$colecao_id, $usuario_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarPorId($id, $usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT l.*, c.nome as colecao_nome 
                FROM livros l
                LEFT JOIN colecoes c ON l.colecao_id = c.id
                WHERE l.id = ? AND l.usuario_id = ?
            ");
            $stmt->execute([$id, $usuario_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function criar($dados)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO livros (
                    usuario_id, colecao_id, titulo, autor, descricao,
                    arquivo_pdf, capa_path, total_paginas, tamanho_mb, 
                    status, tags
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $dados['usuario_id'],
                $dados['colecao_id'] ?? null,
                $dados['titulo'],
                $dados['autor'] ?? null,
                $dados['descricao'] ?? null,
                $dados['arquivo_pdf'],
                $dados['capa_path'] ?? null,
                $dados['total_paginas'] ?? 0,
                $dados['tamanho_mb'] ?? 0,
                $dados['status'] ?? 'quer_ler',
                $dados['tags'] ?? null
            ]);

            return ['success' => true, 'message' => 'Livro adicionado com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao adicionar: ' . $e->getMessage()];
        }
    }

    public function atualizar($id, $usuario_id, $dados)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE livros 
                SET titulo = ?, autor = ?, descricao = ?, 
                    colecao_id = ?, status = ?, tags = ?
                WHERE id = ? AND usuario_id = ?
            ");

            $stmt->execute([
                $dados['titulo'],
                $dados['autor'] ?? null,
                $dados['descricao'] ?? null,
                $dados['colecao_id'] ?? null,
                $dados['status'] ?? 'quer_ler',
                $dados['tags'] ?? null,
                $id,
                $usuario_id
            ]);

            return ['success' => true, 'message' => 'Livro atualizado!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }

    public function deletar($id, $usuario_id)
    {
        try {
            $livro = $this->buscarPorId($id, $usuario_id);

            if (!$livro) {
                return ['success' => false, 'message' => 'Livro não encontrado!'];
            }

            $stmt = $this->db->prepare("
                DELETE FROM livros WHERE id = ? AND usuario_id = ?
            ");
            $stmt->execute([$id, $usuario_id]);

            if ($livro['arquivo_pdf'] && file_exists($livro['arquivo_pdf'])) {
                unlink($livro['arquivo_pdf']);
            }
            if ($livro['capa_path'] && file_exists($livro['capa_path'])) {
                unlink($livro['capa_path']);
            }

            return ['success' => true, 'message' => 'Livro deletado!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }

    public function atualizarProgresso($id, $usuario_id, $pagina_atual)
    {
        try {
            $livro = $this->buscarPorId($id, $usuario_id);

            if (!$livro) {
                return false;
            }

            $porcentagem = 0;
            if ($livro['total_paginas'] > 0) {
                $porcentagem = ($pagina_atual / $livro['total_paginas']) * 100;
            }

            $stmt = $this->db->prepare("
                UPDATE livros 
                SET pagina_atual = ?, 
                    porcentagem_lida = ?,
                    ultima_leitura = CURRENT_TIMESTAMP,
                    status = CASE 
                        WHEN ? >= total_paginas THEN 'concluido'
                        WHEN status = 'quer_ler' THEN 'lendo'
                        ELSE status 
                    END
                WHERE id = ? AND usuario_id = ?
            ");

            $stmt->execute([
                $pagina_atual,
                $porcentagem,
                $pagina_atual,
                $id,
                $usuario_id
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function contarPorUsuario($usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM livros WHERE usuario_id = ?
            ");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetch();
            return $resultado['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function listarPorStatus($usuario_id, $status)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT l.*, c.nome as colecao_nome, c.cor as colecao_cor 
            FROM livros l
            LEFT JOIN colecoes c ON l.colecao_id = c.id
            WHERE l.usuario_id = ? AND l.status = ?
            ORDER BY l.ultima_leitura DESC, l.created_at DESC
        ");
            $stmt->execute([$usuario_id, $status]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function listarPorColecaoEStatus($colecao_id, $usuario_id, $status)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT * FROM livros 
            WHERE colecao_id = ? AND usuario_id = ? AND status = ?
            ORDER BY ultima_leitura DESC, created_at DESC
        ");
            $stmt->execute([$colecao_id, $usuario_id, $status]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function contarPorStatus($usuario_id, $status)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM livros 
            WHERE usuario_id = ? AND status = ?
        ");
            $stmt->execute([$usuario_id, $status]);
            $resultado = $stmt->fetch();
            return $resultado['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function ultimosLidos($usuario_id, $limite = 5)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT l.*, c.nome as colecao_nome, c.cor as colecao_cor 
            FROM livros l
            LEFT JOIN colecoes c ON l.colecao_id = c.id
            WHERE l.usuario_id = ? AND l.status = 'concluido'
            ORDER BY l.data_conclusao DESC, l.updated_at DESC
            LIMIT ?
        ");
            $stmt->execute([$usuario_id, $limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function livrosLendo($usuario_id, $limite = 5)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT l.*, c.nome as colecao_nome, c.cor as colecao_cor 
            FROM livros l
            LEFT JOIN colecoes c ON l.colecao_id = c.id
            WHERE l.usuario_id = ? AND l.status = 'lendo'
            ORDER BY l.ultima_leitura DESC, l.updated_at DESC
            LIMIT ?
        ");
            $stmt->execute([$usuario_id, $limite]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
