<?php
$darkMode = false;
if (isset($_SESSION['usuario_id'])) {
    $prefs = Preferencias::obter($_SESSION['usuario_id']);
    $darkMode = $prefs['dark_mode'] ?? false;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= $darkMode ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Biblioteca Digital' ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    
    <style>
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-gray-100">