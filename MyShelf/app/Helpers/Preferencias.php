<?php

class Preferencias {
    
    public static function obter($usuario_id) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("SELECT preferencias FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $usuario = $stmt->fetch();
            
            if ($usuario && $usuario['preferencias']) {
                return json_decode($usuario['preferencias'], true);
            }
            
            return [
                'dark_mode' => false,
                'tamanho_fonte' => 'medio'
            ];
            
        } catch (PDOException $e) {
            return [
                'dark_mode' => false,
                'tamanho_fonte' => 'medio'
            ];
        }
    }
    
    public static function salvar($usuario_id, $preferencias) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                UPDATE usuarios 
                SET preferencias = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([json_encode($preferencias), $usuario_id]);
            return true;
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public static function alternarDarkMode($usuario_id) {
        $prefs = self::obter($usuario_id);
        $prefs['dark_mode'] = !($prefs['dark_mode'] ?? false);
        self::salvar($usuario_id, $prefs);
        return $prefs['dark_mode'];
    }
}