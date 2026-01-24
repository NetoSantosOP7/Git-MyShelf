<?php
$prefs = Preferencias::obter($_SESSION['usuario_id']);
$darkMode = $prefs['dark_mode'] ?? false;
$titulo = htmlspecialchars($livro['titulo']) . ' - Leitor';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= $darkMode ? 'dark' : '' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= $titulo ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <style>
        body, html { 
            margin: 0; padding: 0; height: 100%; width: 100%; 
            overflow: hidden; background: #111827; overscroll-behavior: none; 
        }
        
        #viewport {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            overflow: auto; display: flex; justify-content: center;
            align-items: flex-start; background: #f3f4f6;
            -webkit-overflow-scrolling: touch;
        }

        html.dark #viewport { background: #1f2937; }

        #canvas-wrapper { display: inline-block; margin: 20px auto; transform-origin: top center; }
        #pdf-canvas { display: block; box-shadow: 0 0 30px rgba(0,0,0,0.3); max-width: 100%; }

        .fixed-bar {
            position: fixed; left: 0; width: 100%; z-index: 9999;
            height: 4rem; background: #1f2937; color: white;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1rem; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
            border-top: 1px solid #374151;
            box-sizing: border-box;
        }

        @media (min-width: 768px) { 
            .fixed-bar { top: 0; bottom: auto; border-top: none; border-bottom: 1px solid #374151; }
            #viewport { padding-top: 4rem; }
        }
        @media (max-width: 767px) { 
            .fixed-bar { bottom: 0; top: auto; }
            #viewport { padding-bottom: 4rem; }
        }

        .dark .fixed-bar { background: #030712; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <div id="barra-ferramentas" class="fixed-bar">
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
            <button id="adicionarMarcador" class="p-2 hover:text-yellow-500"><i class="fas fa-bookmark text-lg"></i></button>
            <button id="verMarcadores" class="p-2 relative">
                <i class="fas fa-list text-lg"></i>
                <span id="countMarcadores" class="absolute top-1 right-0 bg-blue-600 text-[10px] px-1 rounded-full">0</span>
            </button>
            <button id="toggleDarkModeLeitor" class="p-2"><i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?> text-lg"></i></button>
        </div>
    </div>

    <div id="viewport">
        <div id="canvas-wrapper">
            <canvas id="pdf-canvas"></canvas>
        </div>
    </div>

    <div id="modalMarcadores" class="hidden fixed inset-0 bg-black/80 z-[10000] flex items-center justify-center p-4">
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

        let pdfDoc = null, pageNum = <?= $livro['pagina_atual'] ?>, pageRendering = false, scale = 1.0, livroId = <?= $livro['id'] ?>;
        const canvas = document.getElementById('pdf-canvas'), ctx = canvas.getContext('2d'), viewport = document.getElementById('viewport'), barra = document.getElementById('barra-ferramentas');

        if (window.visualViewport) {
            const ajustarBarra = () => {
                const vv = window.visualViewport;
                const offsetBot = (window.innerHeight - vv.height - vv.offsetTop);
                
                barra.style.width = vv.width + 'px';
                barra.style.left = vv.offsetLeft + 'px';
                barra.style.bottom = offsetBot + 'px';
                
                const scaleInv = 1 / vv.scale;
                barra.style.transform = `scale(${scaleInv})`;
                barra.style.transformOrigin = 'bottom left';
                
                barra.style.width = (vv.width * vv.scale) + 'px';
            };

            window.visualViewport.addEventListener('resize', ajustarBarra);
            window.visualViewport.addEventListener('scroll', ajustarBarra);
        }

        function renderPage(num) {
            if (pageRendering) return;
            pageRendering = true;
            pdfDoc.getPage(num).then(page => {
                const dpr = (window.devicePixelRatio || 1) * 1.5; 
                const vPort = page.getViewport({ scale: scale });
                canvas.width = vPort.width * dpr;
                canvas.height = vPort.height * dpr;
                canvas.style.width = vPort.width + 'px';
                canvas.style.height = vPort.height + 'px';
                ctx.scale(dpr, dpr);
                const renderContext = { canvasContext: ctx, viewport: vPort };
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
                const vPort = page.getViewport({ scale: 1 });
                scale = (window.innerWidth < 768) ? (window.innerWidth / vPort.width) : 1.2;
                renderPage(pageNum);
                carregarMarcadores();
            });
        });

        if(document.getElementById('zoom-in')) {
            document.getElementById('zoom-in').onclick = () => { scale += 0.2; renderPage(pageNum); };
            document.getElementById('zoom-out').onclick = () => { if(scale > 0.4) { scale -= 0.2; renderPage(pageNum); } };
        }

        document.getElementById('adicionarMarcador').onclick = async () => {
            const tituloMarcador = prompt("Título do marcador:", "Página " + pageNum);
            if (tituloMarcador) {
                const resp = await fetch('/marcadores/adicionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ livro_id: livroId, pagina: pageNum, titulo: tituloMarcador })
                });
                const data = await resp.json();
                if (data.success) {
                    carregarMarcadores();
                }
            }
        };

        async function carregarMarcadores() {
            const r = await fetch(`/marcadores/listar?livro_id=${livroId}`);
            const d = await r.json();
            const lista = document.getElementById('listaMarcadores');
            const count = document.getElementById('countMarcadores');
            if (d.marcadores) {
                lista.innerHTML = d.marcadores.map(m => `
                    <div class="flex justify-between items-center p-3 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <div onclick="irParaPagina(${m.pagina})" class="cursor-pointer font-medium dark:text-white">${m.titulo} (p. ${m.pagina})</div>
                        <button onclick="eliminarMarcador(${m.id})" class="text-red-500 p-2"><i class="fas fa-trash"></i></button>
                    </div>`).join('');
                count.textContent = d.marcadores.length;
            }
        }

        async function eliminarMarcador(id) {
            if (confirm("Deseja eliminar este marcador?")) {
                const r = await fetch(`/marcadores/eliminar?id=${id}`, { method: 'POST' });
                const d = await r.json();
                if (d.success) carregarMarcadores();
            }
        }

        document.getElementById('prev-page').onclick = () => { if (pageNum > 1 && !pageRendering) { pageNum--; renderPage(pageNum); viewport.scrollTo(0,0); } };
        document.getElementById('next-page').onclick = () => { if (pageNum < pdfDoc.numPages && !pageRendering) { pageNum++; renderPage(pageNum); viewport.scrollTo(0,0); } };
        
        function updateZoomText() { if(document.getElementById('zoom-val')) document.getElementById('zoom-val').textContent = Math.round(scale * 100) + '%'; }
        async function salvarProgresso(p) { fetch('/leitor/salvar-progresso', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ livro_id: livroId, pagina: p }) }); }

        document.getElementById('toggleDarkModeLeitor').onclick = async () => {
            const r = await fetch('/preferencias/dark-mode', { method: 'POST' });
            const d = await r.json();
            if (d.success) {
                document.documentElement.classList.toggle('dark');
                document.querySelector('#toggleDarkModeLeitor i').className = d.dark_mode ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            }
        };

        document.getElementById('verMarcadores').onclick = () => { document.getElementById('modalMarcadores').classList.remove('hidden'); carregarMarcadores(); };
        document.getElementById('fecharModalMarcadores').onclick = () => document.getElementById('modalMarcadores').classList.add('hidden');
        function irParaPagina(p) { pageNum = p; renderPage(p); document.getElementById('modalMarcadores').classList.add('hidden'); viewport.scrollTo(0,0); }
    </script>
</body>
</html>