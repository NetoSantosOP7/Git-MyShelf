<?php 
$titulo = htmlspecialchars($livro['titulo']) . ' - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    
    <div class="mb-6">
        <a href="/livros" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
            <i class="fas fa-arrow-left mr-2"></i>
            Voltar para Livros
        </a>
    </div>
    
    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?= $_SESSION['sucesso'] ?>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 sticky top-4 transition-colors duration-300">
                
                <div class="mb-6">
                    <?php if ($livro['capa_path'] && file_exists(__DIR__ . '/../../../public/' . $livro['capa_path'])): ?>
                        <img 
                            src="/<?= $livro['capa_path'] ?>" 
                            alt="<?= htmlspecialchars($livro['titulo']) ?>"
                            class="w-full rounded-lg shadow-md"
                        >
                    <?php else: ?>
                        <div class="w-full aspect-[3/4] bg-gradient-to-br from-blue-500 to-blue-700 dark:from-blue-600 dark:to-blue-800 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white text-8xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <a 
                    href="/leitor?id=<?= $livro['id'] ?>" 
                    class="w-full bg-blue-600 dark:bg-blue-700 text-white text-center px-6 py-4 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition font-bold text-lg block mb-3"
                >
                    <i class="fas fa-book-open mr-2"></i>
                    <?= $livro['pagina_atual'] > 1 ? 'Continuar Leitura' : 'Começar a Ler' ?>
                </a>
                
                <?php if ($livro['pagina_atual'] > 1): ?>
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Você está na página <?= $livro['pagina_atual'] ?> de <?= $livro['total_paginas'] ?>
                    </p>
                <?php endif; ?>
                
                <a 
                    href="/livros/editar?id=<?= $livro['id'] ?>" 
                    class="w-full bg-green-600 dark:bg-green-700 text-white text-center px-6 py-3 rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition block mb-3"
                >
                    <i class="fas fa-edit mr-2"></i>
                    Editar Informações
                </a>
                
                <a 
                    href="/<?= $livro['arquivo_pdf'] ?>" 
                    download
                    class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-center px-6 py-3 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition block mb-3"
                >
                    <i class="fas fa-download mr-2"></i>
                    Baixar PDF
                </a>
                
                <a 
                    href="/livros/deletar?id=<?= $livro['id'] ?>" 
                    onclick="return confirm('Tem certeza que deseja deletar este livro?')"
                    class="w-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 text-center px-6 py-3 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition block"
                >
                    <i class="fas fa-trash mr-2"></i>
                    Deletar Livro
                </a>
                
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 transition-colors duration-300">
                
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                    <?= htmlspecialchars($livro['titulo']) ?>
                </h1>
                
                <?php if ($livro['autor']): ?>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-6">
                        <i class="fas fa-user mr-2"></i>
                        por <span class="font-semibold"><?= htmlspecialchars($livro['autor']) ?></span>
                    </p>
                <?php endif; ?>
                
                <?php if ($livro['colecao_nome']): ?>
                    <div class="mb-6">
                        <span class="inline-block px-4 py-2 rounded-lg text-white font-semibold" style="background-color: <?= $livro['colecao_cor'] ?? '#3B82F6' ?>;">
                            <i class="fas fa-folder mr-2"></i>
                            <?= htmlspecialchars($livro['colecao_nome']) ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if ($livro['descricao']): ?>
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-align-left mr-2"></i>
                            Descrição
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            <?= nl2br(htmlspecialchars($livro['descricao'])) ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <div class="mb-6">
                    <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-3">
                        <i class="fas fa-bookmark mr-2"></i>
                        Status de Leitura
                    </h3>
                    <?php
                    $statusInfo = [
                        'quer_ler' => ['icon' => 'bookmark', 'text' => 'Quero Ler', 'classes' => 'bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300'],
                        'lendo' => ['icon' => 'book-reader', 'text' => 'Lendo', 'classes' => 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300'],
                        'pausado' => ['icon' => 'pause', 'text' => 'Pausado', 'classes' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300'],
                        'concluido' => ['icon' => 'check-circle', 'text' => 'Concluído', 'classes' => 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300']
                    ];
                    $status = $statusInfo[$livro['status']] ?? $statusInfo['quer_ler'];
                    ?>
                    <span class="inline-block px-4 py-2 <?= $status['classes'] ?> rounded-lg font-semibold">
                        <i class="fas fa-<?= $status['icon'] ?> mr-2"></i>
                        <?= $status['text'] ?>
                    </span>
                </div>
                
                <?php if ($livro['porcentagem_lida'] > 0): ?>
                    <div class="mb-6">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-3">
                            <i class="fas fa-chart-line mr-2"></i>
                            Progresso
                        </h3>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-2">
                            <div 
                                class="bg-blue-600 dark:bg-blue-500 h-4 rounded-full transition-all duration-500" 
                                style="width: <?= $livro['porcentagem_lida'] ?>%"
                            ></div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            <?= round($livro['porcentagem_lida']) ?>% concluído 
                            (<?= $livro['pagina_atual'] ?>/<?= $livro['total_paginas'] ?> páginas)
                        </p>
                    </div>
                <?php endif; ?>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informações do Arquivo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total de Páginas</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                <?= $livro['total_paginas'] ?>
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tamanho do Arquivo</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                <?= $livro['tamanho_mb'] ?> MB
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Adicionado em</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                <?= date('d/m/Y', strtotime($livro['created_at'])) ?>
                            </p>
                        </div>
                        <?php if ($livro['ultima_leitura']): ?>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Última Leitura</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    <?= date('d/m/Y H:i', strtotime($livro['ultima_leitura'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($livro['tags']): ?>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-3">
                            <i class="fas fa-tags mr-2"></i>
                            Tags
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <?php 
                            $tags = explode(',', $livro['tags']);
                            foreach ($tags as $tag): 
                                $tag = trim($tag);
                                if ($tag):
                            ?>
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-sm">
                                    #<?= htmlspecialchars($tag) ?>
                                </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
        
    </div>
    
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>