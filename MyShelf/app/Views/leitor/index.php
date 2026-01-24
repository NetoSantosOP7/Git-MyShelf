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
        tailwind.config = { darkMode: 'class' }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <style>
        body { overflow: hidden; background: #111827; }
        
        #pdf-container { 
            overflow: auto; /* Permite scroll horizontal e vertical para o zoom */
            height: 100vh; 
            background: #f3f4f6; 
            padding-top: 4rem; 
            padding-bottom: 4rem;
            display: flex;
            justify-content: center;
        }
        @media (min-width: 768px) { #pdf-container { padding-bottom: 1rem; } }
        @media (max-width: 767px) { #pdf-container { padding-top: 1rem; } }

        html.dark #pdf-container { background: #1f2937; }
        
        #pdf-canvas {
            display: block;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            max-width: none; /* Importante para o zoom funcionar */
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <div class="fixed left-0 right-0 z-50 bg-gray-800 dark:bg-gray-950 text-white shadow-lg h-16 
                bottom-0 md:bottom-auto md:top-0 border-t md:border-t-0 md:border-b border-gray-700">
        
        <div class="flex items-center justify-between h-full px-2 sm:px-4">
            
            <a href="/livros/detalhes?id=<?= $livro['id'] ?>" class="p-3 hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </a>

            <div class="flex items-center space-x-1 sm:space-x-4">
                <button id="prev-page" class="p-3 text-lg"><i class="fas fa-chevron-left"></i></button>
                
                <div class="flex items-center bg-gray-700 dark:bg-gray-900 rounded px-2 py-1 font-mono">
                    <input type="number" id="page-num-input" class="w-10 bg-transparent text-center focus:outline-none" value="<?= $livro['pagina_atual'] ?>">
                    <span class="text-gray-500">/</span>
                    <span id="page-count">0</span>
                </div>

                <button id="next-page" class="p-3 text-lg"><i class="fas fa-chevron-right"></i></button>
            </div>

            <div class="flex items-center">
                <button id="zoom-btn" class="p-3 hover:text-blue-400" title="Ajustar Zoom">
                    <i class="fas fa-magnifying-glass-plus text-lg"></i>
                    <span id="zoom-val" class="text-[10px] ml-1">Auto</span>
                </button>
                
                <button id="adicionarMarcador" class="p-3 hover:text-yellow-500"><i class="fas fa-bookmark text-lg"></i></button>

                <button id="verMarcadores" class="p-3 hover:text-blue-400 relative">
                    <i class="fas fa-list text-lg"></i>
                    <span id="countMarcadores" class="absolute top-2 right-1 bg-blue-600 text-[10px] px-1 rounded-full">0</span>
                </button>

                <button id="toggleDarkModeLeitor" class="p-3 hover:text-yellow-400">
                    <i class="fas fa-<?= $darkMode ? 'sun' : 'moon' ?> text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="pdf-container">
        <canvas id="pdf-canvas"></canvas>
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

        let pdfDoc = null, 
            pageNum = <?= $livro['pagina_atual'] ?>, 
            pageRendering = false, 
            scale = 1.0, 
            livroId = <?= $livro['id'] ?>;

        const canvas = document.getElementById('pdf-canvas'),
              ctx = canvas.getContext('2d'),
              container = document.getElementById('pdf-container');

        // Carregar Documento
        pdfjsLib.getDocument('/<?= $livro['arquivo_pdf'] ?>').promise.then(pdf => {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            autoScale(); // Define zoom inicial baseado na tela
            renderPage(pageNum);
        });

        // Função de Escala Automática
        function autoScale() {
            const containerWidth = container.clientWidth - 40;
            pdfDoc.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale: 1 });
                scale = containerWidth / viewport.width;
                if (scale > 1.5) scale = 1.5; // Limite para não pixelar
                document.getElementById('zoom-val').textContent = Math.round(scale * 100) + '%';
            });
        }

        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const renderContext = { canvasContext: ctx, viewport: viewport };
                page.render(renderContext).promise.then(() => {
                    pageRendering = false;
                    document.getElementById('page-num-input').value = num;
                    salvarProgresso(num);
                });
            });
        }

        // Lógica do Botão de Zoom Cíclico
        document.getElementById('zoom-btn').onclick = () => {
            if (scale < 1.0) scale = 1.2;
            else if (scale < 1.5) scale = 1.8;
            else if (scale < 2.0) scale = 0.8;
            else scale = 1.0;
            
            document.getElementById('zoom-val').textContent = Math.round(scale * 100) + '%';
            renderPage(pageNum);
        };

        // Navegação
        document.getElementById('prev-page').onclick = () => { if (pageNum > 1) { pageNum--; renderPage(pageNum); } };
        document.getElementById('next-page').onclick = () => { if (pageNum < pdfDoc.numPages) { pageNum++; renderPage(pageNum); } };
        
        // Marcadores e Dark Mode (mantidos do anterior)
        async function salvarProgresso(p) {
            fetch('/leitor/salvar-progresso', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ livro_id: livroId, pagina: p })
            });
        }

        // Script de Marcadores e Tema simplificado
        const toggleDark = async () => {
            const r = await fetch('/preferencias/dark-mode', { method: 'POST' });
            const d = await r.json();
            if (d.success) {
                document.documentElement.classList.toggle('dark');
                document.querySelector('#toggleDarkModeLeitor i').className = d.dark_mode ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            }
        };
        document.getElementById('toggleDarkModeLeitor').onclick = toggleDark;

        // Modal Marcadores
        document.getElementById('verMarcadores').onclick = () => {
            document.getElementById('modalMarcadores').classList.remove('hidden');
            carregarMarcadores();
        };
        document.getElementById('fecharModalMarcadores').onclick = () => document.getElementById('modalMarcadores').classList.add('hidden');
        
        async function carregarMarcadores() {
            const r = await fetch(`/marcadores/listar?livro_id=${livroId}`);
            const d = await r.json();
            const lista = document.getElementById('listaMarcadores');
            lista.innerHTML = d.marcadores.length ? '' : '<p class="text-center py-4">Sem marcadores</p>';
            d.marcadores.forEach(m => {
                const item = document.createElement('div');
                item.className = 'flex justify-between p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded cursor-pointer';
                item.innerHTML = `<div onclick="irParaPagina(${m.pagina})">${m.titulo} (p. ${m.pagina})</div>`;
                lista.appendChild(item);
            });
            document.getElementById('countMarcadores').textContent = d.marcadores.length;
        }

        function irParaPagina(p) { pageNum = p; renderPage(p); document.getElementById('modalMarcadores').classList.add('hidden'); }

        // Atalhos teclado
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft') document.getElementById('prev-page').click();
            if (e.key === 'ArrowRight') document.getElementById('next-page').click();
        });
    </script>
</body>
</html>