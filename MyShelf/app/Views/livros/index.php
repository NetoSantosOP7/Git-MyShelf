<?php
$titulo = 'Meus Livros - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header.php';
?>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">

    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            <i class="fas fa-book mr-2"></i>
            Meus Livros
        </h1>

        <a href="/livros/upload" class="bg-blue-600 dark:bg-blue-700 text-white px-4 sm:px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition">
            <i class="fas fa-upload sm:mr-2"></i>
            <span class="hidden sm:inline">Adicionar Livro</span>
        </a>
    </div>

    <!-- Mensagens -->
    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?= $_SESSION['sucesso'] ?>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= $_SESSION['erro'] ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <!-- Filtros rápidos -->
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
            <a href="/livros?filtro=<?= $key ?>" class="px-4 py-2 rounded-lg whitespace-nowrap transition <?= $filtroAtual === $key ? 'bg-blue-600 dark:bg-blue-700 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' ?>">
                <i class="fas fa-<?= $filtro['icon'] ?> mr-1"></i>
                <?= $filtro['text'] ?>
            </a>
        <?php endforeach; ?>
    </div>
    <!-- Grid de Livros -->
    <?php if (empty($livros)): ?>
        <div class="bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center">
            <i class="fas fa-book-open text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-600 dark:text-gray-400 mb-2">Nenhum livro ainda</h2>
            <p class="text-gray-500 dark:text-gray-500 mb-6">Faça upload do seu primeiro PDF!</p>
            <a href="/livros/upload" class="inline-block bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600">
                <i class="fas fa-upload mr-2"></i>
                Adicionar Primeiro Livro
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php foreach ($livros as $livro): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">

                    <!-- Capa do Livro -->
                    <a href="/livros/detalhes?id=<?= $livro['id'] ?>">
                        <?php if ($livro['capa_path'] && file_exists(__DIR__ . '/../../../public/' . $livro['capa_path'])): ?>
                            <img
                                src="/<?= $livro['capa_path'] ?>"
                                alt="<?= htmlspecialchars($livro['titulo']) ?>"
                                class="w-full h-64 object-cover">
                        <?php else: ?>
                            <div class="w-full h-64 bg-gradient-to-br from-blue-500 to-blue-700 dark:from-blue-600 dark:to-blue-800 flex items-center justify-center">
                                <i class="fas fa-book text-white text-6xl"></i>
                            </div>
                        <?php endif; ?>
                    </a>

                    <!-- Informações -->
                    <div class="p-4">
                        <!-- Badge da Coleção -->
                        <?php if ($livro['colecao_nome']): ?>
                            <span class="inline-block text-xs px-2 py-1 rounded mb-2" style="background-color: <?= $livro['colecao_cor'] ?>20; color: <?= $livro['colecao_cor'] ?>;">
                                <i class="fas fa-folder mr-1"></i>
                                <?= htmlspecialchars($livro['colecao_nome']) ?>
                            </span>
                        <?php endif; ?>

                        <!-- Título -->
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2" title="<?= htmlspecialchars($livro['titulo']) ?>">
                            <?= htmlspecialchars(mb_substr($livro['titulo'], 0, 40)) ?>
                            <?= mb_strlen($livro['titulo']) > 40 ? '...' : '' ?>
                        </h3>

                        <!-- Autor -->
                        <?php if ($livro['autor']): ?>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-1">
                                <i class="fas fa-user text-xs mr-1"></i>
                                <?= htmlspecialchars($livro['autor']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Status e Progresso -->
                        <div class="mb-3">
                            <?php
                            $statusInfo = [
                                'quer_ler' => ['icon' => 'bookmark', 'text' => 'Quero Ler', 'classes' => 'bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300'],
                                'lendo' => ['icon' => 'book-reader', 'text' => 'Lendo', 'classes' => 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300'],
                                'pausado' => ['icon' => 'pause', 'text' => 'Pausado', 'classes' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300'],
                                'concluido' => ['icon' => 'check-circle', 'text' => 'Concluído', 'classes' => 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300']
                            ];
                            $status = $statusInfo[$livro['status']] ?? $statusInfo['quer_ler'];
                            ?>
                            <span class="text-xs px-2 py-1 <?= $status['classes'] ?> rounded">
                                <i class="fas fa-<?= $status['icon'] ?> mr-1"></i>
                                <?= $status['text'] ?>
                            </span>

                            <!-- Barra de progresso se estiver lendo -->
                            <?php if ($livro['status'] === 'lendo' && $livro['porcentagem_lida'] > 0): ?>
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full"
                                            style="width: <?= $livro['porcentagem_lida'] ?>%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?= round($livro['porcentagem_lida']) ?>% concluído
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Informações adicionais -->
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-3 space-y-1">
                            <div>
                                <i class="fas fa-file-pdf mr-1"></i>
                                <?= $livro['total_paginas'] ?> páginas
                            </div>
                            <div>
                                <i class="fas fa-hdd mr-1"></i>
                                <?= $livro['tamanho_mb'] ?> MB
                            </div>
                        </div>

                        <!-- Botões de ação -->
                        <div class="flex space-x-2">

                            <a href="/leitor?id=<?= $livro['id'] ?>"
                               class="flex-1 bg-blue-600 dark:bg-blue-700 text-white text-center px-3 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition text-sm">
                                <i class="fas fa-book-open sm:mr-1"></i>
                                <span class="hidden sm:inline"><?= $livro['pagina_atual'] > 1 ? 'Continuar' : 'Ler' ?></span>
                            </a>

                            <a href="/livros/detalhes?id=<?= $livro['id'] ?>"
                               class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                               title="Detalhes">
                                <i class="fas fa-info-circle"></i>
                            </a>

                            <a href="/livros/deletar?id=<?= $livro['id'] ?>"
                               class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 px-3 py-2 rounded hover:bg-red-200 dark:hover:bg-red-800 transition"
                               title="Deletar"
                               onclick="return confirm('Tem certeza que deseja deletar este livro?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>