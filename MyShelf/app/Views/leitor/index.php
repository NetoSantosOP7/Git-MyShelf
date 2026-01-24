<?php
$prefs = Preferencias::obter($_SESSION['usuario_id']);
$darkMode = $prefs['dark_mode'] ?? false;
$titulo = htmlspecialchars($livro['titulo']) . ' - Leitor';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= $darkMode ? 'dark' : '' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <style>
        * {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body {
            overflow: hidden;
        }

        #pdf-canvas {
            max-width: 100%;
            margin: 0 auto;
            display: block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #pdf-container {
            overflow-y: auto;
            height: calc(100vh - 64px);
            background: #f3f4f6;
            padding: 20px;
        }

        html.dark #pdf-container {
            background: #1f2937;
        }

        html.dark body {
            background: #111827;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <div class="bg-gray-800 dark:bg-gray-950 text-white px-2 sm:px-4 py-3 flex items-center justify-between shadow-lg h-16 relative">

        <div class="flex items-center space-x-2 sm:space-x-4 overflow-hidden">
            <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="hover:text-gray-300" title="Fechar">
                <i class="fas fa-times text-xl"></i>
            </a>
            <div class="overflow-hidden">
                <h1 class="font-bold text-md sm:text-lg truncate"><?= htmlspecialchars($livro['titulo']) ?></h1>
                <?php if ($livro['autor']): ?>
                    <p class="text-xs sm:text-sm text-gray-400 truncate"><?= htmlspecialchars($livro['autor']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center space-x-1 sm:space-x-2">
            <button id="prev-page" class="px-3 py-2 bg-gray-700 dark:bg-gray-800 rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition" title="Página Anterior (←)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="flex items-center space-x-1">
                <input type="number" id="page-num-input" class="w-14 px-1 py-1 bg-gray-700 dark:bg-gray-800 text-white text-center rounded border border-gray-600 dark:border-gray-700" min="1" value="<?= $livro['pagina_atual'] ?>">
                <span class="hidden sm:inline">/</span>
                <span id="page-count" class="hidden sm:inline">0</span>
            </div>
            <button id="next-page" class="px-3 py-2 bg-gray-700 dark:bg-gray-800 rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition" title="Próxima Página (→)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="hidden lg:flex items-center space-x-2">
            <div class="flex items-center space-x-2 ml-4">
                <button id="zoom-out" class="px-3 py-2 bg-gray-700 dark:bg-gray-800 rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition" title="Diminuir Zoom">
                    <i class="fas fa-search-minus"></i>
                </button>
                <span id="zoom-level" class="w-16 text-center">100%</span>
                <button id="zoom-in" class="px-3 py-2 bg-gray-700 dark:bg-gray-800 rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition" title="Aumentar Zoom">
                    <i class="fas fa-search-plus"></i>
                </button>
            </div>

            <div class="flex items-center space-x-2 ml-4 border-l border-gray-600 dark:border-gray-700 pl-4">
                <button id="adicionarMarcador" class="px-3 py-2 bg-yellow-600 dark:bg-yellow-700 rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition" title="Adicionar Marcador">
                    <i class="fas fa-bookmark"></i>
                </button>
                <button id="verMarcadores" class="px-3 py-2 bg-gray-700 dark:bg-gray-800 rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition" title="Ver Marcadores">
                    <i class="fas fa-list"></i>
                    <span id="countMarcadores" class="ml-1">0</span>
                </button>
            </div>

            <div class="text-sm ml-4 border-l border-gray-600 pl-4">
                <span id="progress-text">0%</span>
            </div>

            <button id="toggleDarkModeLeitor" class="px-3 py-2 bg-gray-700 dark:bg-gray-800 rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition ml-4" title="Modo Escuro">
                <i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?>"></i>
            </button>

            <a href="/<?= $livro['arquivo_pdf'] ?>" download class="px-3 py-2 bg-blue-600 dark:bg-blue-700 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition ml-2" title="Baixar PDF">
                <i class="fas fa-download"></i>
            </a>
        </div>
        
        <div class="lg:hidden">
            <button id="leitor-menu-button" class="px-3 py-2 text-white focus:outline-none">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>

        <div id="leitor-menu" class="hidden absolute top-16 right-2 bg-gray-700 dark:bg-gray-800 rounded-md shadow-lg p-2 w-60 z-50">
            <div class="p-2">
                <span class="text-sm font-bold text-gray-400">Zoom</span>
                <div class="flex items-center justify-between space-x-2 mt-1">
                    <button id="zoom-out-mobile" class="flex-1 py-2 bg-gray-600 dark:bg-gray-900 rounded hover:bg-gray-500 dark:hover:bg-gray-700 transition"><i class="fas fa-search-minus"></i></button>
                    <span id="zoom-level-mobile" class="text-center">100%</span>
                    <button id="zoom-in-mobile" class="flex-1 py-2 bg-gray-600 dark:bg-gray-900 rounded hover:bg-gray-500 dark:hover:bg-gray-700 transition"><i class="fas fa-search-plus"></i></button>
                </div>
            </div>

            <hr class="border-gray-600 dark:border-gray-700 my-1">

            <button id="adicionarMarcador-mobile" class="w-full text-left flex items-center p-2 hover:bg-gray-600 dark:hover:bg-gray-900 rounded transition">
                <i class="fas fa-bookmark fa-fw mr-2 text-yellow-500"></i> Adicionar Marcador
            </button>
            <button id="verMarcadores-mobile" class="w-full text-left flex items-center justify-between p-2 hover:bg-gray-600 dark:hover:bg-gray-900 rounded transition">
                <span><i class="fas fa-list fa-fw mr-2"></i> Ver Marcadores</span>
                <span id="countMarcadores-mobile" class="bg-blue-600 text-xs px-2 py-1 rounded-full">0</span>
            </button>

            <hr class="border-gray-600 dark:border-gray-700 my-1">

            <button id="toggleDarkModeLeitor-mobile" class="w-full text-left flex items-center p-2 hover:bg-gray-600 dark:hover:bg-gray-900 rounded transition">
                <i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?> fa-fw mr-2"></i> Modo Escuro
            </button>

            <hr class="border-gray-600 dark:border-gray-700 my-1">

             <a href="/<?= $livro['arquivo_pdf'] ?>" download class="w-full text-left flex items-center p-2 hover:bg-gray-600 dark:hover:bg-gray-900 rounded transition">
                <i class="fas fa-download fa-fw mr-2"></i> Baixar PDF
            </a>
        </div>
    </div>

    <div id="pdf-container" class="p-2 sm:p-4 md:p-5">
        <canvas id="pdf-canvas"></canvas>

        <div id="loading" class="text-center py-20">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400 dark:text-gray-600"></i>
            <p class="text-gray-600 dark:text-gray-400 mt-4">Carregando PDF...</p>
        </div>
    </div>

    <div id="modalMarcadores" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                    <i class="fas fa-bookmark text-yellow-600 dark:text-yellow-500 mr-2"></i>
                    Marcadores
                </h3>
                <button id="fecharModalMarcadores" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="listaMarcadores" class="space-y-2">
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Nenhum marcador ainda</p>
            </div>
        </div>
    </div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = <?= $livro['pagina_atual'] ?>;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.5;
        const livroId = <?= $livro['id'] ?>;

        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        const loading = document.getElementById('loading');

        pdfjsLib.getDocument('/<?= $livro['arquivo_pdf'] ?>').promise.then(function(pdf) {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            loading.style.display = 'none';
            renderPage(pageNum);
        });

        function renderPage(num) {
            pageRendering = true;

            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({
                    scale: scale
                });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;

                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }

                    document.getElementById('page-num-input').value = num;
                    updateProgress();

                    salvarProgresso(num);
                });
            });
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }
        
        function updateZoomUI() {
            const zoomText = Math.round(scale * 100) + '%';
            document.getElementById('zoom-level').textContent = zoomText;
            document.getElementById('zoom-level-mobile').textContent = zoomText;
        }

        document.getElementById('prev-page').addEventListener('click', function() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        });

        document.getElementById('next-page').addEventListener('click', function() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        });

        document.getElementById('page-num-input').addEventListener('change', function() {
            let num = parseInt(this.value);
            if (num < 1) num = 1;
            if (num > pdfDoc.numPages) num = pdfDoc.numPages;
            pageNum = num;
            queueRenderPage(pageNum);
        });

        function zoomIn() {
            if (scale >= 3) return;
            scale += 0.25;
            updateZoomUI();
            queueRenderPage(pageNum);
        }

        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.25;
            updateZoomUI();
            queueRenderPage(pageNum);
        }

        document.getElementById('zoom-in').addEventListener('click', zoomIn);
        document.getElementById('zoom-out').addEventListener('click', zoomOut);
        document.getElementById('zoom-in-mobile').addEventListener('click', zoomIn);
        document.getElementById('zoom-out-mobile').addEventListener('click', zoomOut);


        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT') return; 
            if (e.key === 'ArrowLeft') {
                document.getElementById('prev-page').click();
            } else if (e.key === 'ArrowRight') {
                document.getElementById('next-page').click();
            }
        });

        function updateProgress() {
            if (!pdfDoc) return;
            const percent = ((pageNum / pdfDoc.numPages) * 100).toFixed(1);
            document.getElementById('progress-text').textContent = percent + '%';
        }

        function salvarProgresso(pagina) {
            fetch('/leitor/salvar-progresso', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    livro_id: livroId,
                    pagina: pagina
                })
            });
        }

        const menuButton = document.getElementById('leitor-menu-button');
        const menu = document.getElementById('leitor-menu');
        
        menuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && !menuButton.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        let marcadores = [];

        async function carregarMarcadores() {
            try {
                const response = await fetch(`/marcadores/listar?livro_id=${livroId}`);
                const data = await response.json();

                if (data.success) {
                    marcadores = data.marcadores;
                    atualizarListaMarcadores();
                    document.getElementById('countMarcadores').textContent = marcadores.length;
                    document.getElementById('countMarcadores-mobile').textContent = marcadores.length;
                }
            } catch (error) {
                console.error('Erro ao carregar marcadores:', error);
            }
        }

        async function adicionarMarcador() {
             const titulo = prompt('Título do marcador (opcional):', `Página ${pageNum}`);

            if (titulo === null) return; 

            try {
                const response = await fetch('/marcadores/adicionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        livro_id: livroId,
                        pagina: pageNum,
                        titulo: titulo || `Página ${pageNum}`
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert('✓ Marcador adicionado!');
                    carregarMarcadores();
                } else {
                    alert('✗ ' + data.message);
                }
            } catch (error) {
                alert('Erro ao adicionar marcador');
            }
        }
        
        function verMarcadores() {
            document.getElementById('modalMarcadores').classList.remove('hidden');
        }

        document.getElementById('adicionarMarcador').addEventListener('click', adicionarMarcador);
        document.getElementById('adicionarMarcador-mobile').addEventListener('click', adicionarMarcador);
        document.getElementById('verMarcadores').addEventListener('click', verMarcadores);
        document.getElementById('verMarcadores-mobile').addEventListener('click', verMarcadores);

        document.getElementById('fecharModalMarcadores').addEventListener('click', function() {
            document.getElementById('modalMarcadores').classList.add('hidden');
        });
        
        document.getElementById('modalMarcadores').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        function atualizarListaMarcadores() {
            const lista = document.getElementById('listaMarcadores');
            if (marcadores.length === 0) {
                lista.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Nenhum marcador ainda</p>';
                return;
            }
            lista.innerHTML = '';
            marcadores.forEach(marcador => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded hover:bg-gray-100 dark:hover:bg-gray-600 transition';
                div.innerHTML = `
                    <button onclick="irParaPagina(${marcador.pagina})" class="flex-1 text-left">
                        <p class="font-semibold text-gray-800 dark:text-gray-100">${marcador.titulo}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Página ${marcador.pagina}</p>
                    </button>
                    <button onclick="deletarMarcador(${marcador.id})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 ml-3">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                lista.appendChild(div);
            });
        }

        function irParaPagina(pagina) {
            pageNum = pagina;
            queueRenderPage(pageNum);
            document.getElementById('modalMarcadores').classList.add('hidden');
        }

        async function deletarMarcador(id) {
            if (!confirm('Remover este marcador?')) return;
            try {
                const response = await fetch('/marcadores/deletar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, livro_id: livroId })
                });
                const data = await response.json();
                if (data.success) carregarMarcadores();
            } catch (error) {
                alert('Erro ao deletar marcador');
            }
        }
        
        carregarMarcadores();

        async function toggleDarkModeLeitor() {
            try {
                const response = await fetch('/preferencias/dark-mode', { method: 'POST' });
                const data = await response.json();
                if (data.success) {
                    document.documentElement.classList.toggle('dark');
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
                console.error('Erro:', error);
            }
        }

        document.getElementById('toggleDarkModeLeitor')?.addEventListener('click', toggleDarkModeLeitor);
        document.getElementById('toggleDarkModeLeitor-mobile')?.addEventListener('click', toggleDarkModeLeitor);
    </script>
</body>

</html>