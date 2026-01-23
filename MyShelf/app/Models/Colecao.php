<?php

class Colecao
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
                SELECT * FROM colecoes 
                WHERE usuario_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarPorId($id, $usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM colecoes 
                WHERE id = ? AND usuario_id = ?
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
                INSERT INTO colecoes (usuario_id, nome, descricao, tipo, cor, icone) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $dados['usuario_id'],
                $dados['nome'],
                $dados['descricao'] ?? null,
                $dados['tipo'] ?? 'livros',
                $dados['cor'] ?? '#3B82F6',
                $dados['icone'] ?? 'book'
            ]);

            return ['success' => true, 'message' => 'Coleção criada com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao criar coleção: ' . $e->getMessage()];
        }
    }

    public function atualizar($id, $usuario_id, $dados)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE colecoes 
                SET nome = ?, descricao = ?, tipo = ?, cor = ?, icone = ?
                WHERE id = ? AND usuario_id = ?
            ");

            $stmt->execute([
                $dados['nome'],
                $dados['descricao'] ?? null,
                $dados['tipo'] ?? 'livros',
                $dados['cor'] ?? '#3B82F6',
                $dados['icone'] ?? 'book',
                $id,
                $usuario_id
            ]);

            return ['success' => true, 'message' => 'Coleção atualizada com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }

    public function deletar($id, $usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM colecoes 
                WHERE id = ? AND usuario_id = ?
            ");

            $stmt->execute([$id, $usuario_id]);

            return ['success' => true, 'message' => 'Coleção deletada com sucesso!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao deletar: ' . $e->getMessage()];
        }
    }

    public function contarPorUsuario($usuario_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM colecoes WHERE usuario_id = ?
            ");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetch();
            return $resultado['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function contarLivros($colecao_id)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM livros WHERE colecao_id = ?
        ");
            $stmt->execute([$colecao_id]);
            $resultado = $stmt->fetch();
            return $resultado['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}