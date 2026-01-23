<?php
$titulo = 'Minhas Coleções - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header.php';
?>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-8">
        <h1 class="dark:text-white text-3xl font-bold text-gray-800">
            <i class="fas fa-folder-open mr-2"></i>
            Minhas Coleções
        </h1>

        <a href="/colecoes/criar" class="bg-blue-600 text-white px-4 sm:px-6 py-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus sm:mr-2"></i>
            <span class="hidden sm:inline">Nova Coleção</span>
        </a>
    </div>

    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?= $_SESSION['sucesso'] ?>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= $_SESSION['erro'] ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (empty($colecoes)): ?>
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
            <i class="fas fa-folder-open text-6xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-600 mb-2">Nenhuma coleção ainda</h2>
            <p class="text-gray-500 mb-6">Crie sua primeira coleção para organizar seus livros!</p>
            <a href="/colecoes/criar" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Criar Primeira Coleção
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($colecoes as $colecao): ?>
                <div class="dark:bg-gray-800 bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">

                    <div class="p-6" style="background-color: <?= htmlspecialchars($colecao['cor']) ?>;">
                        <div class="flex items-center justify-between text-white">
                            <i class="fas fa-<?= htmlspecialchars($colecao['icone']) ?> text-4xl"></i>
                            <span class="text-sm opacity-80"><?= ucfirst($colecao['tipo']) ?></span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="dark:text-white text-xl font-bold text-gray-800 mb-2">
                            <?= htmlspecialchars($colecao['nome']) ?>
                        </h3>

                        <?php if ($colecao['descricao']): ?>
                            <p class="dark:text-gray-300 text-gray-600 text-sm mb-4">
                                <?= htmlspecialchars($colecao['descricao']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="flex items-center justify-between pt-4 border-t">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-book mr-1"></i>
                                <?php
                                $colecaoModel = new Colecao();
                                $totalLivros = $colecaoModel->contarLivros($colecao['id']);
                                echo $totalLivros . ' ' . ($totalLivros == 1 ? 'livro' : 'livros');
                                ?>
                            </span>

                            <div class="flex space-x-2">
                                <a href="/colecoes/ver?id=<?= $colecao['id'] ?>"
                                    class="text-green-600 hover:text-green-800"
                                    title="Ver livros">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/colecoes/editar?id=<?= $colecao['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/colecoes/deletar?id=<?= $colecao['id'] ?>"
                                    class="text-red-600 hover:text-red-800"
                                    title="Deletar"
                                    onclick="return confirm('Tem certeza que deseja deletar esta coleção?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>