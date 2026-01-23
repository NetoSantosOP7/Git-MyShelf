<?php
class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($nome, $email, $senha) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email já cadastrado!'];
            }
            
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nome, email, senha) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$nome, $email, $senhaHash]);
            
            return ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $senha) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            $usuario = $stmt->fetch();
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                return ['success' => true, 'message' => 'Login realizado!'];
            }
            
            return ['success' => false, 'message' => 'Email ou senha incorretos!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao fazer login: ' . $e->getMessage()];
        }
    }
    
    public static function estaLogado() {
        return isset($_SESSION['usuario_id']);
    }
    
    public static function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}