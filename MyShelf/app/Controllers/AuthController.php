<?php

class AuthController {
    
    public function login() {
        if (Usuario::estaLogado()) {
            header('Location: /dashboard');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($email) || empty($senha)) {
                $erro = 'Preencha todos os campos!';
            } else {
                $usuario = new Usuario();
                $resultado = $usuario->login($email, $senha);
                
                if ($resultado['success']) {
                    header('Location: /dashboard');
                    exit;
                } else {
                    $erro = $resultado['message'];
                }
            }
        }
        
        require_once __DIR__ . '/../Views/auth/login.php';
    }
    
    public function register() {
        if (Usuario::estaLogado()) {
            header('Location: /dashboard');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $confirma_senha = $_POST['confirma_senha'] ?? '';
            
            if (empty($nome) || empty($email) || empty($senha)) {
                $erro = 'Preencha todos os campos!';
            } elseif ($senha !== $confirma_senha) {
                $erro = 'As senhas não conferem!';
            } elseif (strlen($senha) < 6) {
                $erro = 'A senha deve ter no mínimo 6 caracteres!';
            } else {
                $usuario = new Usuario();
                $resultado = $usuario->register($nome, $email, $senha);
                
                if ($resultado['success']) {
                    $sucesso = $resultado['message'];
                } else {
                    $erro = $resultado['message'];
                }
            }
        }
        
        require_once __DIR__ . '/../Views/auth/register.php';
    }
    
    public function logout() {
        Usuario::logout();
    }
}