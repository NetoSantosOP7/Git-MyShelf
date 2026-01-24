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
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <style>
        body { margin: 0; overflow: hidden; height: 100vh; width: 100vw; position: fixed; }
        
        #pdf-container { 
            overflow: auto; 
            height: 100vh; 
            width: 100vw;
            background: #f3f4f6; 
            display: block; 
            padding-top: 4rem;
            padding-bottom: 4rem;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 768px) { #pdf-container { padding-bottom: 0; } }
        @media (max-width: 767px) { #pdf-container { padding-top: 0; } }

        html.dark #pdf-container { background: #1f2937; }
        
        #pdf-canvas {
            display: block;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
            margin: 10px auto;
            image-rendering: high-quality;
            transform-origin: 0 0; 
            transition: transform 0.1s ease-out;
        }

        .fixed-bar {
            position: fixed;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 4rem;
            touch-action: none;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <div class="fixed-bar bg-gray-800 dark:bg-gray-950 text-white shadow-lg 
                bottom-0 md:bottom-auto md:top-0 border-t md:border-t-0 md:border-b border-gray-700">
        
        <div class="flex items-center justify-between h-full px-4">
            <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="p-2"><i class="fas fa-times text-xl"></i></a>

            <div class="flex items-center space-x-2">
                <button id="prev-page" class="p-3"><i class="fas fa-chevron-left text-lg"></i></button>
                <div class="bg-gray-700 dark:bg-gray-900 rounded px-3 py-1 font-mono text-sm">
                    <input type="number" id="page-num-input" class="w-8 bg-transparent text-center focus:outline-none" value="<?= $livro['pagina_atual'] ?>">
                    <span>/ <span id="page-count">0</span></span>
                </div>
                <button id="next-page" class="p-3"><i class="fas fa-chevron-right text-lg"></i></button>
            </div>

            <div class="flex items-center space-x-1">
                <div class="hidden md:flex items-center border-r border-gray-600 pr-2 mr-2">
                    <button id="zoom-out" class="p-2 hover:text-blue-400"><i class="fas fa-search-minus"></i></button>
                    <span id="zoom-val" class="text-xs w-10 text-center">100%</span>
                    <button id="zoom-in" class="p-2 hover:text-blue-400"><i class="fas fa-search-plus"></i></button>
                </div>
                <button id="adicionarMarcador" class="p-2"><i class="fas fa-bookmark"></i></button>
                <button id="verMarcadores" class="p-2 relative">
                    <i class="fas fa-list"></i>
                    <span id="countMarcadores" class="absolute top-1 right-0 bg-blue-600 text-[10px] px-1 rounded-full">0</span>
                </button>
                <button id="toggleDarkModeLeitor" class="p-2"><i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?>"></i></button>
            </div>
        </div>
    </div>

    <div id="pdf-container">
        <div id="canvas-wrapper" style="display: inline-block; min-width: 100%; text-align: center;">
            <canvas id="pdf-canvas"></canvas>
        </div>
    </div>

    <div id="modalMarcadores" class="hidden fixed inset-0 bg-black/80 z-[2000] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-md p-4 shadow-2xl">
            <div class="flex justify-between mb-4 border-b dark:border-gray-700 pb-2">
                <h3 class="font-bold dark:text-white">Marcadores</h3>
                <button id="fecharModalMarcadores"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div id="listaMarcadores" class="max-h-[50vh] overflow-y-auto"></div>
        </div>
    </div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null, pageNum = <?= $livro['pagina_atual'] ?>, pageRendering = false, scale = 1.0, livroId = <?= $livro['id'] ?>, renderTimeout = null, isPinched = false;
        const canvas = document.getElementById('pdf-canvas'), ctx = canvas.getContext('2d'), container = document.getElementById('pdf-container');

        function renderPage(num) {
            if (pageRendering) return;
            pageRendering = true;
            
            pdfDoc.getPage(num).then(page => {
                const dpr = window.devicePixelRatio || 1;
                const viewport = page.getViewport({ scale: scale });
                
                canvas.width = viewport.width * dpr;
                canvas.height = viewport.height * dpr;
                canvas.style.width = viewport.width + 'px';
                canvas.style.height = viewport.height + 'px';
                
                canvas.style.transform = "scale(1)";
                ctx.scale(dpr, dpr);
                
                const renderContext = { canvasContext: ctx, viewport: viewport };
                page.render(renderContext).promise.then(() => {
                    pageRendering = false;
                    document.getElementById('page-num-input').value = num;
                    updateZoomText();
                    salvarProgresso(num);
                });
            });
        }

        pdfjsLib.getDocument('/<?= $livro['arquivo_pdf'] ?>').promise.then(pdf => {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            pdfDoc.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale: 1 });
                const padding = window.innerWidth < 768 ? 0 : 40;
                scale = (container.clientWidth - padding) / viewport.width;
                renderPage(pageNum);
            });
        });

        if(document.getElementById('zoom-in')) {
            document.getElementById('zoom-in').onclick = () => { scale += 0.2; renderPage(pageNum); };
            document.getElementById('zoom-out').onclick = () => { if(scale > 0.3) { scale -= 0.2; renderPage(pageNum); } };
        }

        let initialDist = null;
        let startScale = 1.0;

        container.addEventListener('touchstart', e => {
            if (e.touches.length === 2) {
                isPinched = true;
                initialDist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                startScale = scale;
            }
        }, { passive: true });

        container.addEventListener('touchmove', e => {
            if (e.touches.length === 2 && initialDist) {
                const dist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                const factor = dist / initialDist;
                canvas.style.transform = `scale(${factor})`;
                scale = startScale * factor;
            }
        }, { passive: true });

        container.addEventListener('touchend', e => {
            if (isPinched && e.touches.length < 2) {
                isPinched = false;
                initialDist = null;
                clearTimeout(renderTimeout);
                renderTimeout = setTimeout(() => { renderPage(pageNum); }, 200);
            }
        });

        document.getElementById('prev-page').onclick = () => { if (pageNum > 1 && !pageRendering) { pageNum--; renderPage(pageNum); } };
        document.getElementById('next-page').onclick = () => { if (pageNum < pdfDoc.numPages && !pageRendering) { pageNum++; renderPage(pageNum); } };
        
        function updateZoomText() { if(document.getElementById('zoom-val')) document.getElementById('zoom-val').textContent = Math.round(scale * 100) + '%'; }
        async function salvarProgresso(p) { fetch('/leitor/salvar-progresso', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ livro_id: livroId, pagina: p }) }); }

        document.getElementById('toggleDarkModeLeitor').onclick = async () => {
            const r = await fetch('/preferencias/dark-mode', { method: 'POST' });
            const d = await r.json();
            if (d.success) {
                document.documentElement.classList.toggle('dark');
                document.querySelector('#toggleDarkModeLeitor i').className = d.dark_mode ? 'fas fa-sun' : 'fas fa-moon';
            }
        };

        document.getElementById('verMarcadores').onclick = () => { document.getElementById('modalMarcadores').classList.remove('hidden'); carregarMarcadores(); };
        document.getElementById('fecharModalMarcadores').onclick = () => document.getElementById('modalMarcadores').classList.add('hidden');
        
        async function carregarMarcadores() {
            const r = await fetch(`/marcadores/listar?livro_id=${livroId}`);
            const d = await r.json();
            const lista = document.getElementById('listaMarcadores');
            lista.innerHTML = d.marcadores.map(m => `<div onclick="irParaPagina(${m.pagina})" class="p-3 border-b dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 font-medium dark:text-white">${m.titulo} <span class="text-xs text-gray-500">(p. ${m.pagina})</span></div>`).join('');
            document.getElementById('countMarcadores').textContent = d.marcadores.length;
        }

        function irParaPagina(p) { pageNum = p; renderPage(p); document.getElementById('modalMarcadores').classList.add('hidden'); }
    </script>
</body>
</html>