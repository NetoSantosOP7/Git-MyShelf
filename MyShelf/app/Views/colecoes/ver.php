<?php
$titulo = htmlspecialchars($colecao['nome']) . ' - Coleção';
require_once __DIR__ . '/../layouts/header.php';
?>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">

    <div class="mb-8">
        <a href="/colecoes" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>
            Voltar para Coleções
        </a>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 rounded-lg flex items-center justify-center text-white text-4xl" style="background-color: <?= $colecao['cor'] ?>;">
                        <i class="fas fa-<?= $colecao['icone'] ?>"></i>
                    </div>

                    <div>
                        <h1 class="text-4xl font-bold text-gray-800 mb-2">
                            <?= htmlspecialchars($colecao['nome']) ?>
                        </h1>
                        <?php if ($colecao['descricao']): ?>
                            <p class="text-gray-600 mb-3"><?= htmlspecialchars($colecao['descricao']) ?></p>
                        <?php endif; ?>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>
                                <i class="fas fa-bookmark mr-1"></i>
                                <?= ucfirst($colecao['tipo']) ?>
                            </span>
                            <span>
                                <i class="fas fa-book mr-1"></i>
                                <?= count($livros) ?> <?= count($livros) == 1 ? 'livro' : 'livros' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="/livros/upload" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Adicionar Livro
                    </a>
                    <a href="/colecoes/editar?id=<?= $colecao['id'] ?>" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-edit mr-2"></i>
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
    $filtroAtual = $_GET['filtro'] ?? 'todos';
    $filtros = [
        'todos' => ['icon' => 'border-all', 'text' => 'Todos'],
        'lendo' => ['icon' => 'book-reader', 'text' => 'Lendo'],
        'quer_ler' => ['icon' => 'bookmark', 'text' => 'Quero Ler'],
        'concluido' => ['icon' => 'check-circle', 'text' => 'Concluídos'],
        'pausado' => ['icon' => 'pause', 'text' => 'Pausados']
    ];
    ?>
    <div class="flex space-x-4 mb-8 overflow-x-auto pb-2">
        <?php foreach ($filtros as $key => $filtro): ?>
            <a
                href="/colecoes/ver?id=<?= $colecao['id'] ?>&filtro=<?= $key ?>"
                class="px-4 py-2 rounded-lg whitespace-nowrap transition <?= $filtroAtual === $key ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                <i class="fas fa-<?= $filtro['icon'] ?> mr-1"></i>
                <?= $filtro['text'] ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($livros)): ?>
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
            <i class="fas fa-book-open text-6xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-600 mb-2">Nenhum livro nesta coleção</h2>
            <p class="text-gray-500 mb-6">Adicione livros a esta coleção para começar</p>
            <a href="/livros/upload" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Adicionar Primeiro Livro
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php foreach ($livros as $livro): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">

                    <a href="/livros/detalhes?id=<?= $livro['id'] ?>">
                        <?php if ($livro['capa_path'] && file_exists(__DIR__ . '/../../../public/' . $livro['capa_path'])): ?>
                            <img
                                src="/<?= $livro['capa_path'] ?>"
                                alt="<?= htmlspecialchars($livro['titulo']) ?>"
                                class="w-full h-64 object-cover">
                        <?php else: ?>
                            <div class="w-full h-64 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                                <i class="fas fa-book text-white text-6xl"></i>
                            </div>
                        <?php endif; ?>
                    </a>

                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-1 line-clamp-2" title="<?= htmlspecialchars($livro['titulo']) ?>">
                            <?= htmlspecialchars(mb_substr($livro['titulo'], 0, 40)) ?>
                            <?= mb_strlen($livro['titulo']) > 40 ? '...' : '' ?>
                        </h3>

                        <?php if ($livro['autor']): ?>
                            <p class="text-sm text-gray-600 mb-2 line-clamp-1">
                                <i class="fas fa-user text-xs mr-1"></i>
                                <?= htmlspecialchars($livro['autor']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="mb-3">
                            <?php
                            $statusInfo = [
                                'quer_ler' => ['icon' => 'bookmark', 'text' => 'Quero Ler', 'color' => 'gray'],
                                'lendo' => ['icon' => 'book-reader', 'text' => 'Lendo', 'color' => 'blue'],
                                'pausado' => ['icon' => 'pause', 'text' => 'Pausado', 'color' => 'yellow'],
                                'concluido' => ['icon' => 'check-circle', 'text' => 'Concluído', 'color' => 'green']
                            ];
                            $status = $statusInfo[$livro['status']] ?? $statusInfo['quer_ler'];
                            ?>
                            <span class="text-xs px-2 py-1 bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-700 rounded">
                                <i class="fas fa-<?= $status['icon'] ?> mr-1"></i>
                                <?= $status['text'] ?>
                            </span>

                            <?php if ($livro['status'] === 'lendo' && $livro['porcentagem_lida'] > 0): ?>
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full"
                                            style="width: <?= $livro['porcentagem_lida'] ?>%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?= round($livro['porcentagem_lida']) ?>%
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex space-x-2">
                            <a
                                href="/leitor?id=<?= $livro['id'] ?>"
                                class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded hover:bg-blue-700 transition text-sm">
                                <i class="fas fa-book-open mr-1"></i>
                                <?= $livro['pagina_atual'] > 1 ? 'Continuar' : 'Ler' ?>
                            </a>
                            <a
                                href="/livros/detalhes?id=<?= $livro['id'] ?>"
                                class="bg-gray-200 text-gray-700 px-3 py-2 rounded hover:bg-gray-300 transition"
                                title="Detalhes">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>