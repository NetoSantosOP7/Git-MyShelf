<?php
class Marcador {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function listarPorLivro($livro_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM marcadores 
                WHERE livro_id = ? 
                ORDER BY pagina ASC
            ");
            $stmt->execute([$livro_id]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            return [];
        }
    }
    public function adicionar($livro_id, $pagina, $titulo = null) {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM marcadores 
                WHERE livro_id = ? AND pagina = ?
            ");
            $stmt->execute([$livro_id, $pagina]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Já existe um marcador nesta página'];
            }
            $stmt = $this->db->prepare("
                INSERT INTO marcadores (livro_id, pagina, titulo) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$livro_id, $pagina, $titulo]);
            
            return ['success' => true, 'message' => 'Marcador adicionado!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    public function deletar($id, $livro_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM marcadores 
                WHERE id = ? AND livro_id = ?
            ");
            $stmt->execute([$id, $livro_id]);
            
            return ['success' => true, 'message' => 'Marcador removido!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    public function temMarcador($livro_id, $pagina) {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM marcadores 
                WHERE livro_id = ? AND pagina = ?
            ");
            $stmt->execute([$livro_id, $pagina]);
            
            return $stmt->fetch() ? true : false;
            
        } catch (PDOException $e) {
            return false;
        }
    }
}