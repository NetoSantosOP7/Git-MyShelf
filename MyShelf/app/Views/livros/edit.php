<?php 
$titulo = 'Editar Livro - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<!-- Navbar -->
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    
    <div class="max-w-3xl mx-auto">
        
        <!-- Cabeçalho -->
        <div class="flex items-center mb-8">
            <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-edit mr-2"></i>
                Editar Livro
            </h1>
        </div>
        
        <!-- Mensagens -->
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $_SESSION['erro'] ?>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>
        
        <!-- Formulário -->
        <form method="POST" action="/livros/atualizar" class="bg-white rounded-lg shadow-lg p-8">
            
            <input type="hidden" name="id" value="<?= $livro['id'] ?>">
            
            <!-- Título -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-heading mr-1"></i> Título *
                </label>
                <input 
                    type="text" 
                    name="titulo" 
                    required
                    maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    value="<?= htmlspecialchars($livro['titulo']) ?>"
                >
            </div>
            
            <!-- Autor -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-user-edit mr-1"></i> Autor
                </label>
                <input 
                    type="text" 
                    name="autor" 
                    maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    value="<?= htmlspecialchars($livro['autor'] ?? '') ?>"
                >
            </div>
            
            <!-- Descrição -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-align-left mr-1"></i> Descrição
                </label>
                <textarea 
                    name="descricao" 
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                ><?= htmlspecialchars($livro['descricao'] ?? '') ?></textarea>
            </div>
            
            <!-- Grid 2 colunas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <!-- Coleção -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-folder mr-1"></i> Coleção
                    </label>
                    <select 
                        name="colecao_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    >
                        <option value="">Sem coleção</option>
                        <?php foreach ($colecoes as $col): ?>
                            <option value="<?= $col['id'] ?>" <?= $livro['colecao_id'] == $col['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($col['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-bookmark mr-1"></i> Status
                    </label>
                    <select 
                        name="status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    >
                        <option value="quer_ler" <?= $livro['status'] == 'quer_ler' ? 'selected' : '' ?>>📚 Quero Ler</option>
                        <option value="lendo" <?= $livro['status'] == 'lendo' ? 'selected' : '' ?>>📖 Lendo</option>
                        <option value="pausado" <?= $livro['status'] == 'pausado' ? 'selected' : '' ?>>⏸️ Pausado</option>
                        <option value="concluido" <?= $livro['status'] == 'concluido' ? 'selected' : '' ?>>✅ Concluído</option>
                    </select>
                </div>
                
            </div>
            
            <!-- Tags -->
            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-tags mr-1"></i> Tags
                </label>
                <input 
                    type="text" 
                    name="tags" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    placeholder="Ex: ficção, aventura, fantasia (separadas por vírgula)"
                    value="<?= htmlspecialchars($livro['tags'] ?? '') ?>"
                >
            </div>
            
            <!-- Botões -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>
                
                <button 
                    type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-bold"
                >
                    <i class="fas fa-save mr-2"></i>
                    Salvar Alterações
                </button>
            </div>
            
        </form>
        
    </div>
    
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>