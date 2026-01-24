<?php
$prefs = Preferencias::obter($_SESSION['usuario_id']);
$darkMode = $prefs['dark_mode'] ?? false;
$titulo = htmlspecialchars($livro['titulo']) . ' - Leitor';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= $darkMode ? 'dark' : '' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
        * { transition: background-color 0.3s ease, color 0.3s ease; }
        body { overflow: hidden; touch-action: manipulation; }
        #pdf-canvas { max-width: 100%; margin: 0 auto; display: block; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        
        #pdf-container { 
            overflow-y: auto; 
            height: 100vh; 
            background: #f3f4f6; 
            padding-top: 4rem; 
            padding-bottom: 4rem;
        }
        @media (min-width: 768px) { #pdf-container { padding-bottom: 1rem; } }
        @media (max-width: 767px) { #pdf-container { padding-top: 1rem; } }

        html.dark #pdf-container { background: #1f2937; }
        html.dark body { background: #111827; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <div class="fixed left-0 right-0 z-50 bg-gray-800 dark:bg-gray-950 text-white shadow-lg h-16 
                bottom-0 md:bottom-auto md:top-0 border-t md:border-t-0 md:border-b border-gray-700">
        
        <div class="flex items-center justify-between h-full px-2 sm:px-4">
            
            <div class="flex-none">
                <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="p-3 hover:text-gray-300" title="Fechar">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            <div class="flex items-center space-x-1 sm:space-x-4">
                <button id="prev-page" class="p-3 text-lg active:scale-95"><i class="fas fa-chevron-left"></i></button>
                
                <div class="flex items-center bg-gray-700 dark:bg-gray-900 rounded px-2 py-1">
                    <input type="number" id="page-num-input" class="w-10 bg-transparent text-center focus:outline-none text-sm" value="<?= $livro['pagina_atual'] ?>">
                    <span class="text-gray-500 mx-1">/</span>
                    <span id="page-count" class="text-sm">0</span>
                </div>

                <button id="next-page" class="p-3 text-lg active:scale-95"><i class="fas fa-chevron-right"></i></button>
            </div>

            <div class="flex items-center">
                <button id="zoom-in" class="p-3 hover:text-blue-400 active:scale-95" title="Aumentar Zoom">
                    <i class="fas fa-search-plus text-lg"></i>
                </button>
                
                <button id="adicionarMarcador" class="p-3 hover:text-yellow-500 active:scale-95" title="Marcar Página">
                    <i class="fas fa-bookmark text-lg"></i>
                </button>

                <button id="verMarcadores" class="p-3 hover:text-blue-400 active:scale-95 relative" title="Ver Marcadores">
                    <i class="fas fa-list text-lg"></i>
                    <span id="countMarcadores" class="absolute top-2 right-1 bg-blue-600 text-[10px] px-1 rounded-full">0</span>
                </button>

                <button id="toggleDarkModeLeitor" class="p-3 hover:text-yellow-400 active:scale-95" title="Alternar Tema">
                    <i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?> text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="pdf-container">
        <canvas id="pdf-canvas"></canvas>
        <div id="loading" class="text-center py-20">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
            <p class="text-gray-500 mt-4">Abrindo livro...</p>
        </div>
    </div>

    <div id="modalMarcadores" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-[100] p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold dark:text-white">Marcadores</h3>
                <button id="fecharModalMarcadores" class="text-gray-400 p-1"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div id="listaMarcadores" class="p-2 max-h-[60vh] overflow-y-auto space-y-1"></div>
        </div>
    </div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null, pageNum = <?= $livro['pagina_atual'] ?>, pageRendering = false, pageNumPending = null, scale = 1.1, livroId = <?= $livro['id'] ?>;
        const canvas = document.getElementById('pdf-canvas'), ctx = canvas.getContext('2d'), loading = document.getElementById('loading');

        // Detectar se é mobile para ajustar o zoom inicial
        if(window.innerWidth < 768) scale = 0.8;

        pdfjsLib.getDocument('/<?= $livro['arquivo_pdf'] ?>').promise.then(pdf => {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            loading.style.display = 'none';
            renderPage(pageNum);
        });

        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                const renderContext = { canvasContext: ctx, viewport: viewport };
                page.render(renderContext).promise.then(() => {
                    pageRendering = false;
                    if (pageNumPending !== null) { renderPage(pageNumPending); pageNumPending = null; }
                    document.getElementById('page-num-input').value = num;
                    salvarProgresso(num);
                });
            });
        }

        function queueRenderPage(num) { pageRendering ? pageNumPending = num : renderPage(num); }

        document.getElementById('prev-page').onclick = () => { if (pageNum > 1) { pageNum--; queueRenderPage(pageNum); } };
        document.getElementById('next-page').onclick = () => { if (pageNum < pdfDoc.numPages) { pageNum++; queueRenderPage(pageNum); } };
        document.getElementById('page-num-input').onchange = e => { 
            let n = parseInt(e.target.value); 
            if (n >= 1 && n <= pdfDoc.numPages) { pageNum = n; queueRenderPage(n); }
        };

        // Botão de Zoom Único (Ciclo: 1.0 -> 1.5 -> 2.0 -> 0.8)
        document.getElementById('zoom-in').onclick = () => {
            if (scale < 1.1) scale = 1.1;
            else if (scale < 1.6) scale = 1.6;
            else if (scale < 2.1) scale = 2.1;
            else scale = 0.8;
            renderPage(pageNum);
        };

        async function salvarProgresso(p) {
            fetch('/leitor/salvar-progresso', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ livro_id: livroId, pagina: p })
            });
        }

        let marcadores = [];
        async function carregarMarcadores() {
            const r = await fetch(`/marcadores/listar?livro_id=${livroId}`);
            const d = await r.json();
            if (d.success) {
                marcadores = d.marcadores;
                document.getElementById('countMarcadores').textContent = marcadores.length;
                atualizarListaMarcadores();
            }
        }

        function atualizarListaMarcadores() {
            const lista = document.getElementById('listaMarcadores');
            lista.innerHTML = marcadores.length ? '' : '<p class="text-center py-4 text-gray-400">Sem marcadores</p>';
            marcadores.forEach(m => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded cursor-pointer';
                item.innerHTML = `
                    <div onclick="irParaPagina(${m.pagina})" class="flex-1">
                        <div class="text-sm font-bold dark:text-white">${m.titulo}</div>
                        <div class="text-xs text-gray-500">Página ${m.pagina}</div>
                    </div>
                    <button onclick="deletarMarcador(${m.id})" class="text-gray-400 hover:text-red-500 px-2"><i class="fas fa-trash-alt"></i></button>
                `;
                lista.appendChild(item);
            });
        }

        document.getElementById('adicionarMarcador').onclick = async () => {
            const t = prompt('Nome do marcador:', `Página ${pageNum}`);
            if (t) {
                const r = await fetch('/marcadores/adicionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ livro_id: livroId, pagina: pageNum, titulo: t })
                });
                if ((await r.json()).success) carregarMarcadores();
            }
        };

        function irParaPagina(p) { pageNum = p; queueRenderPage(p); document.getElementById('modalMarcadores').classList.add('hidden'); }
        async function deletarMarcador(id) {
            if (confirm('Excluir marcador?')) {
                await fetch('/marcadores/deletar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, livro_id: livroId })
                });
                carregarMarcadores();
            }
        }

        document.getElementById('verMarcadores').onclick = () => document.getElementById('modalMarcadores').classList.remove('hidden');
        document.getElementById('fecharModalMarcadores').onclick = () => document.getElementById('modalMarcadores').classList.add('hidden');

        document.getElementById('toggleDarkModeLeitor').onclick = async () => {
            const r = await fetch('/preferencias/dark-mode', { method: 'POST' });
            const d = await r.json();
            if (d.success) {
                document.documentElement.classList.toggle('dark');
                document.querySelector('#toggleDarkModeLeitor i').classList.toggle('fa-sun', d.dark_mode);
                document.querySelector('#toggleDarkModeLeitor i').classList.toggle('fa-moon', !d.dark_mode);
            }
        };

        carregarMarcadores();
    </script>
</body>
</html>