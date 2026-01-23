<?php
$titulo = 'Editar Coleção - Biblioteca Digital';
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
                <i class="fas fa-edit mr-2"></i>
                Editar Coleção
            </h1>
        </div>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $_SESSION['erro'] ?>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <form method="POST" action="/colecoes/atualizar" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 transition-colors duration-300">

            <input type="hidden" name="id" value="<?= $colecao['id'] ?>">

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
                    value="<?= htmlspecialchars($colecao['nome']) ?>">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-align-left mr-1"></i> Descrição (opcional)
                </label>
                <textarea
                    name="descricao"
                    rows="3"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 dark:text-white transition-colors"
                    placeholder="Uma breve descrição sobre esta coleção..."><?= htmlspecialchars($colecao['descricao'] ?? '') ?></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-list mr-1"></i> Tipo de Conteúdo
                </label>
                <select
                    name="tipo"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-blue-500 text-gray-800 dark:text-white transition-colors">
                    <option value="livros" <?= $colecao['tipo'] === 'livros' ? 'selected' : '' ?>>📚 Livros</option>
                    <option value="mangas" <?= $colecao['tipo'] === 'mangas' ? 'selected' : '' ?>>🎌 Mangás</option>
                    <option value="hqs" <?= $colecao['tipo'] === 'hqs' ? 'selected' : '' ?>>🦸 HQs/Comics</option>
                    <option value="revistas" <?= $colecao['tipo'] === 'revistas' ? 'selected' : '' ?>>📰 Revistas</option>
                    <option value="artigos" <?= $colecao['tipo'] === 'artigos' ? 'selected' : '' ?>>📄 Artigos</option>
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
                        value="<?= $colecao['cor'] ?>"
                        class="h-12 w-20 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded cursor-pointer">
                    <div id="corPreview" class="flex-1 h-12 rounded-lg" style="background-color: <?= $colecao['cor'] ?>;"></div>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">
                    <i class="fas fa-icons mr-1"></i> Ícone
                </label>
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                    <?php
                    $icones = [
                        'book', 'book-open', 'book-bookmark', 'bookmark', 'dragon', 'fire',
                        'star', 'heart', 'crown', 'magic', 'flask', 'wand-magic-sparkles',
                        'robot', 'rocket', 'atom', 'dna', 'graduation-cap', 'brain',
                        'lightbulb', 'code', 'gamepad', 'chess', 'puzzle-piece', 'dice'
                    ];
                    foreach ($icones as $icone):
                        $selecionado = ($icone === $colecao['icone']);
                    ?>
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="icone"
                                value="<?= $icone ?>"
                                <?= $selecionado ? 'checked' : '' ?>
                                class="hidden icone-radio">
                            <div class="icone-option border-2 rounded-lg p-4 text-center hover:border-blue-500 transition-all <?= $selecionado ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/40' : 'border-gray-300 dark:border-gray-600 bg-transparent' ?>">
                                <i class="fas fa-<?= $icone ?> text-2xl <?= $selecionado ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-400' ?>"></i>
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
                    Salvar Alterações
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
            const icon = optionDiv.querySelector('i');
            
            optionDiv.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/40', 'border-gray-300', 'dark:border-gray-600');
            icon.classList.remove('text-blue-600', 'dark:text-blue-400', 'text-gray-700', 'dark:text-gray-400');

            if (radio.checked) {
                optionDiv.classList.add('border-blue-500', isDarkMode ? 'dark:bg-blue-900/40' : 'bg-blue-50');
                icon.classList.add(isDarkMode ? 'dark:text-blue-400' : 'text-blue-600');
            } else {
                optionDiv.classList.add(isDarkMode ? 'dark:border-gray-600' : 'border-gray-300');
                icon.classList.add(isDarkMode ? 'dark:text-gray-400' : 'text-gray-700');
            }
        });
    }

    iconeRadios.forEach(radio => {
        radio.addEventListener('change', updateIconSelection);
    });

    document.addEventListener('DOMContentLoaded', updateIconSelection);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>