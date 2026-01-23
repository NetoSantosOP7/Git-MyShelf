<?php

class PreferenciasController {
    
    public function alternarDarkMode() {
        if (!Usuario::estaLogado()) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $darkMode = Preferencias::alternarDarkMode($_SESSION['usuario_id']);
        
        echo json_encode([
            'success' => true,
            'dark_mode' => $darkMode
        ]);
        exit;
    }
}