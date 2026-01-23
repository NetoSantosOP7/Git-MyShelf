<?php
$darkMode = false;
if (isset($_SESSION['usuario_id'])) {
    $prefs = Preferencias::obter($_SESSION['usuario_id']);
    $darkMode = $prefs['dark_mode'] ?? false;
}
?>

<nav class="bg-blue-600 dark:bg-gray-800 text-white shadow-lg">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-book-reader text-2xl"></i>
                <a href="/dashboard" class="text-xl font-bold hover:text-gray-200">Biblioteca Digital</a>
            </div>

            <div class="hidden md:flex items-center space-x-6">
                <a href="/dashboard" class="hover:text-gray-200">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
                <a href="/colecoes" class="hover:text-gray-200">
                    <i class="fas fa-folder mr-1"></i> Coleções
                </a>
                <a href="/livros" class="hover:text-gray-200">
                    <i class="fas fa-book mr-1"></i> Livros
                </a>
                
                <button 
                    id="toggleDarkMode" 
                    class="hover:text-gray-200 transition"
                    title="Alternar modo escuro"
                >
                    <i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?> text-xl"></i>
                </button>
                
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        <?= $_SESSION['usuario_nome'] ?>
                    </span>
                    <a href="/logout" class="hover:text-gray-200">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Sair
                    </a>
                </div>
            </div>

            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-white focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden px-4 pb-4">
        <a href="/dashboard" class="block py-2 hover:bg-blue-700 dark:hover:bg-gray-700 rounded"><i class="fas fa-home mr-1"></i> Dashboard</a>
        <a href="/colecoes" class="block py-2 hover:bg-blue-700 dark:hover:bg-gray-700 rounded"><i class="fas fa-folder mr-1"></i> Coleções</a>
        <a href="/livros" class="block py-2 hover:bg-blue-700 dark:hover:bg-gray-700 rounded"><i class="fas fa-book mr-1"></i> Livros</a>
        <hr class="my-2 border-gray-500"/>
        <div class="flex justify-between items-center">
             <span class="text-white">
                <i class="fas fa-user mr-2"></i>
                <?= $_SESSION['usuario_nome'] ?>
            </span>
            <div>
                <button 
                    id="toggleDarkModeMobile" 
                    class="hover:text-gray-200 transition"
                    title="Alternar modo escuro"
                >
                    <i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?> text-xl"></i>
                </button>
                <a href="/logout" class="hover:text-gray-200 ml-4">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    Sair
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});

async function toggleDarkModeRequest() {
    try {
        const response = await fetch('/preferencias/dark-mode', {
            method: 'POST'
        });
        const data = await response.json();
        
        if (data.success) {
            document.documentElement.classList.toggle('dark', data.dark_mode);
            
            document.querySelectorAll('.fa-sun, .fa-moon').forEach(icon => {
                if (data.dark_mode) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            });
        }
    } catch (error) {
        console.error('Erro ao alternar dark mode:', error);
    }
}

document.getElementById('toggleDarkMode')?.addEventListener('click', toggleDarkModeRequest);

document.getElementById('toggleDarkModeMobile')?.addEventListener('click', toggleDarkModeRequest);
</script>