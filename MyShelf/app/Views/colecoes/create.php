<?php
$titulo = 'Nova Coleção - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header.php';
?>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">

    <div class="max-w-2xl mx-auto">

        <div class="flex items-center mb-8">
            <a href="/colecoes" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-folder-plus mr-2"></i>
                Nova Coleção
            </h1>
        </div>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $_SESSION['erro'] ?>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>


        <style>
            .icone-radio:checked+.icone-option {
                background-color: #EFF6FF;
                border-color: #3B82F6;
            }

            .icone-radio:checked+.icone-option i {
                color: #1D4ED8;
            }

            .dark .icone-radio:checked+.icone-option {
                background-color: #1E3A8A;
                border-color: #60A5FA;
                color: white;
            }

            .dark .icone-radio:checked+.icone-option i {
                color: #BFDBFE;
            }
        </style>



        <form method="POST" action="/colecoes/salvar" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 transition-colors duration-300">

            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-tag mr-1"></i> Nome da Coleção *
                </label>
                <input
                    type="text"
                    name="nome"
                    required
                    maxlength="150"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 dark:text-white transition-colors"
                    placeholder="Ex: Mangás Naruto, Série Harry Potter..."
                    value="<?= $_POST['nome'] ?? '' ?>">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-align-left mr-1"></i> Descrição (opcional)
                </label>
                <textarea
                    name="descricao"
                    rows="3"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 dark:text-white transition-colors"
                    placeholder="Uma breve descrição sobre esta coleção..."><?= $_POST['descricao'] ?? '' ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-list mr-1"></i> Tipo de Conteúdo
                </label>
                <select
                    name="tipo"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 dark:text-white transition-colors">
                    <option value="livros">📚 Livros</option>
                    <option value="mangas">🎌 Mangás</option>
                    <option value="hqs">🦸 HQs/Comics</option>
                    <option value="revistas">📰 Revistas</option>
                    <option value="artigos">📄 Artigos</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-palette mr-1"></i> Cor da Coleção
                </label>
                <div class="flex items-center space-x-4">
                    <input
                        type="color"
                        name="cor"
                        id="corPicker"
                        value="#3B82F6"
                        class="h-12 w-20 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                    <div id="corPreview" class="flex-1 h-12 rounded-lg" style="background-color: #3B82F6;"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Escolha uma cor para identificar visualmente esta coleção
                </p>
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-icons mr-1"></i> Ícone
                </label>
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                    <?php
                    $icones = [
                        'book', 
                        'book-open', 
                        'book-bookmark',
                        'bookmark', 
                        'dragon', 
                        'fire',
                        'star', 
                        'heart', 
                        'crown', 
                        'magic', 
                        'flask', 
                        'wand-magic-sparkles',
                        'robot', 
                        'rocket', 
                        'atom', 
                        'dna', 
                        'graduation-cap', 
                        'brain',
                        'lightbulb', 
                        'code', 
                        'gamepad', 
                        'chess', 
                        'puzzle-piece', 
                        'dice'
                    ];
                    foreach ($icones as $icone):
                    ?>
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="icone"
                                value="<?= $icone ?>"
                                <?= $icone === 'book' ? 'checked' : '' ?>
                                class="hidden icone-radio">
                            <div class="icone-option border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200 bg-transparent">
                                <i class="fas fa-<?= $icone ?> text-2xl text-gray-700 dark:text-gray-400"></i>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="/colecoes" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-8 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition font-bold shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Criar Coleção
                </button>
            </div>

        </form>

    </div>

</div>

<script>
    const corPicker = document.getElementById('corPicker');
    const corPreview = document.getElementById('corPreview');

    corPicker.addEventListener('input', function() {
        corPreview.style.backgroundColor = this.value;
    });

    const iconeRadios = document.querySelectorAll('.icone-radio');

    function updateIconSelection() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        
        iconeRadios.forEach(radio => {
            const optionDiv = radio.nextElementSibling;
            if (radio.checked) {
                if (isDarkMode) {
                    optionDiv.style.backgroundColor = '#1E3A8A';
                    optionDiv.style.borderColor = '#60A5FA';
                } else {
                    optionDiv.style.backgroundColor = '#EFF6FF';
                    optionDiv.style.borderColor = '#3B82F6';
                }
            } else {
                optionDiv.style.backgroundColor = 'transparent';
                optionDiv.style.borderColor = isDarkMode ? '#4B5563' : '#D1D5DB'; // gray-600 ou gray-300
            }
        });
    }

    iconeRadios.forEach(radio => {
        radio.addEventListener('change', updateIconSelection);
    });

    document.addEventListener('DOMContentLoaded', updateIconSelection);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>