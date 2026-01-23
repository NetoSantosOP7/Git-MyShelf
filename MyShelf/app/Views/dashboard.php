<?php 
$titulo = 'Dashboard - Biblioteca Digital';
require_once __DIR__ . '/layouts/header.php'; 
?>

<?php require_once __DIR__ . '/layouts/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Saudação -->
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">
            Bem-vindo de volta, <?= explode(' ', $_SESSION['usuario_nome'])[0] ?>! 👋
        </h1>
        <p class="text-gray-600 dark:text-gray-400">Aqui está um resumo da sua biblioteca</p>
    </div>
    
    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total de Livros -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Total de Livros</p>
                    <h3 class="text-4xl font-bold"><?= $totalLivros ?></h3>
                </div>
                <i class="fas fa-book text-5xl text-blue-200 opacity-50"></i>
            </div>
            <a href="/livros" class="text-sm text-blue-100 hover:text-white mt-4 inline-block">
                Ver todos →
            </a>
        </div>
        
        <!-- Lendo Agora -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-700 dark:to-green-800 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Lendo Agora</p>
                    <h3 class="text-4xl font-bold"><?= $lendo ?></h3>
                </div>
                <i class="fas fa-book-reader text-5xl text-green-200 opacity-50"></i>
            </div>
            <a href="/livros?filtro=lendo" class="text-sm text-green-100 hover:text-white mt-4 inline-block">
                Continuar leitura →
            </a>
        </div>
        
        <!-- Concluídos -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-700 dark:to-purple-800 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">Concluídos</p>
                    <h3 class="text-4xl font-bold"><?= $concluidos ?></h3>
                </div>
                <i class="fas fa-check-circle text-5xl text-purple-200 opacity-50"></i>
            </div>
            <a href="/livros?filtro=concluido" class="text-sm text-purple-100 hover:text-white mt-4 inline-block">
                Ver concluídos →
            </a>
        </div>
        
        <!-- Coleções -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-700 dark:to-orange-800 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm mb-1">Coleções</p>
                    <h3 class="text-4xl font-bold"><?= $totalColecoes ?></h3>
                </div>
                <i class="fas fa-folder-open text-5xl text-orange-200 opacity-50"></i>
            </div>
            <a href="/colecoes" class="text-sm text-orange-100 hover:text-white mt-4 inline-block">
                Organizar →
            </a>
        </div>
        
    </div>
    
    <!-- Grid Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna 1 e 2: Livros que está lendo -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        <i class="fas fa-book-reader text-blue-600 dark:text-blue-400 mr-2"></i>
                        Continue Lendo
                    </h2>
                    <?php if (!empty($livrosLendo)): ?>
                        <a href="/livros?filtro=lendo" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                            Ver todos
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($livrosLendo)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-book-open text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Você não está lendo nenhum livro no momento</p>
                        <a href="/livros?filtro=quer_ler" class="inline-block bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600">
                            <i class="fas fa-bookmark mr-2"></i>
                            Ver lista "Quero Ler"
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($livrosLendo as $livro): ?>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md dark:hover:bg-gray-700 transition">
                                <div class="flex items-start space-x-4">
                                    <!-- Capa -->
                                    <a href="/livros/detalhes?id=<?= $livro['id'] ?>">
                                        <?php if ($livro['capa_path'] && file_exists(__DIR__ . '/../../public/' . $livro['capa_path'])): ?>
                                            <img 
                                                src="/<?= $livro['capa_path'] ?>" 
                                                alt="Capa"
                                                class="w-20 h-28 object-cover rounded shadow"
                                            >
                                        <?php else: ?>
                                            <div class="w-20 h-28 bg-blue-500 dark:bg-blue-600 rounded flex items-center justify-center">
                                                <i class="fas fa-book text-white text-2xl"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    
                                    <!-- Info -->
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1">
                                            <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                <?= htmlspecialchars($livro['titulo']) ?>
                                            </a>
                                        </h3>
                                        <?php if ($livro['autor']): ?>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                <i class="fas fa-user text-xs mr-1"></i>
                                                <?= htmlspecialchars($livro['autor']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <!-- Progresso -->
                                        <div class="mb-2">
                                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                <span>Página <?= $livro['pagina_atual'] ?> de <?= $livro['total_paginas'] ?></span>
                                                <span><?= round($livro['porcentagem_lida']) ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div 
                                                    class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all" 
                                                    style="width: <?= $livro['porcentagem_lida'] ?>%"
                                                ></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Botão -->
                                        <a 
                                            href="/leitor?id=<?= $livro['id'] ?>" 
                                            class="inline-block bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition text-sm"
                                        >
                                            <i class="fas fa-book-open sm:mr-1"></i>
                                            <span class="hidden sm:inline">Continuar Leitura</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Coluna 3: Sidebar -->
        <div class="space-y-6">
            
            <!-- Ações Rápidas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                    Ações Rápidas
                </h3>
                <div class="space-y-3">
                    <a href="/livros/upload" class="block w-full bg-blue-600 dark:bg-blue-700 text-white text-center px-4 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition">
                        <i class="fas fa-upload mr-2"></i>
                        Adicionar Livro
                    </a>
                    <a href="/colecoes/criar" class="block w-full bg-green-600 dark:bg-green-700 text-white text-center px-4 py-3 rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition">
                        <i class="fas fa-folder-plus mr-2"></i>
                        Nova Coleção
                    </a>
                    <a href="/livros?filtro=quer_ler" class="block w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-center px-4 py-3 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        <i class="fas fa-bookmark mr-2"></i>
                        Lista "Quero Ler" (<?= $querLer ?>)
                    </a>
                </div>
            </div>
            
            <!-- Últimos Concluídos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Últimos Concluídos
                </h3>
                
                <?php if (empty($ultimosLidos)): ?>
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">
                        Nenhum livro concluído ainda
                    </p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($ultimosLidos as $livro): ?>
                            <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <?php if ($livro['capa_path'] && file_exists(__DIR__ . '/../../public/' . $livro['capa_path'])): ?>
                                    <img 
                                        src="/<?= $livro['capa_path'] ?>" 
                                        alt="Capa"
                                        class="w-12 h-16 object-cover rounded shadow-sm"
                                    >
                                <?php else: ?>
                                    <div class="w-12 h-16 bg-green-500 dark:bg-green-600 rounded flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">
                                        <?= htmlspecialchars($livro['titulo']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-check mr-1"></i>
                                        Concluído
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Coleções Recentes -->
            <?php if (!empty($ultimasColecoes)): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                        <i class="fas fa-folder text-orange-500 mr-2"></i>
                        Coleções
                    </h3>
                    <div class="space-y-2">
                        <?php foreach ($ultimasColecoes as $col): ?>
                            <a 
                                href="/colecoes/ver?id=<?= $col['id'] ?>" 
                                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                style="border-left: 4px solid <?= $col['cor'] ?>;"
                            >
                                <i class="fas fa-<?= $col['icone'] ?> text-xl" style="color: <?= $col['cor'] ?>;"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100">
                                        <?= htmlspecialchars($col['nome']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php 
                                        $colecaoModel = new Colecao();
                                        $total = $colecaoModel->contarLivros($col['id']);
                                        echo $total . ' ' . ($total == 1 ? 'livro' : 'livros');
                                        ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="/colecoes" class="block text-center text-blue-600 dark:text-blue-400 hover:underline text-sm mt-4">
                        Ver todas as coleções →
                    </a>
                </div>
            <?php endif; ?>
            
        </div>
        
    </div>
    
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>