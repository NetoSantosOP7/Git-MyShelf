<?php 
$titulo = 'Login - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header_auth.php'; 
?>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 dark:bg-gray-800">
        <div class="text-center mb-8">
            <i class="fas fa-book-reader text-5xl text-blue-600 mb-3"></i>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Biblioteca Digital</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Faça login para continuar</p>
        </div>
        <?php if (isset($erro)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $erro ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/login">
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                    <i class="fas fa-envelope mr-1"></i> Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 bg-white dark:bg-gray-700 dark:text-gray-100"
                    placeholder="seu@email.com"
                    value="<?= $_POST['email'] ?? '' ?>"
                >
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">
                    <i class="fas fa-lock mr-1"></i> Senha
                </label>
                <input 
                    type="password" 
                    name="senha" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 bg-white dark:bg-gray-700 dark:text-gray-100"
                    placeholder="••••••••"
                >
            </div>
            <button 
                type="submit"
                class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition duration-200"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Entrar
            </button>
        </form>
        <p class="text-center mt-6 text-gray-600 dark:text-gray-300">
            Não tem conta? 
            <a href="/register" class="text-blue-600 hover:underline font-semibold dark:hover:text-blue-400">
                Cadastre-se
            </a>
        </p>
        
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_auth.php'; ?>