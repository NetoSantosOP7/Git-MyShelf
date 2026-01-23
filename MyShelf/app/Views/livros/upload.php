<?php 
$titulo = 'Upload de Livro - Biblioteca Digital';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<!-- Navbar -->
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="max-w-4xl mx-auto">
        
        <!-- Cabeçalho -->
        <div class="flex items-center mb-8">
            <a href="/livros" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                <i class="fas fa-upload mr-2"></i>
                Adicionar Livro
            </h1>
        </div>
        
        <!-- Mensagens -->
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $_SESSION['erro'] ?>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>
        
        <!-- Formulário -->
        <form method="POST" action="/livros/salvar" enctype="multipart/form-data" id="uploadForm" class="bg-white rounded-lg shadow-lg p-6 sm:p-8">
            
            <!-- Upload do PDF -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-file-pdf mr-1"></i> Arquivo PDF * (máx. 50MB)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 sm:p-8 text-center hover:border-blue-500 transition">
                    <input 
                        type="file" 
                        name="pdf" 
                        accept=".pdf"
                        required
                        id="pdfInput"
                        class="hidden"
                    >
                    <label for="pdfInput" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-5xl sm:text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">Clique para selecionar o arquivo PDF</p>
                        <p class="text-sm text-gray-500">ou arraste e solte aqui</p>
                        <p id="nomeArquivo" class="text-blue-600 font-semibold mt-4"></p>
                    </label>
                </div>
                <p id="processandoPdf" class="text-sm text-blue-600 mt-2 hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Processando PDF e gerando capa...
                </p>
            </div>

            <!-- Preview da Capa Gerada -->
            <div id="previewCapaContainer" class="mb-6 hidden">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-image mr-1"></i> Capa Gerada Automaticamente
                </label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <img id="previewCapaImg" class="w-32 h-auto rounded shadow mx-auto sm:mx-0" alt="Preview da capa">
                        <div class="flex-1 text-center sm:text-left">
                            <p class="text-green-600 font-semibold mb-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                Capa extraída da primeira página do PDF
                            </p>
                            <p class="text-sm text-gray-600 mb-3">
                                Você pode substituir por uma capa personalizada abaixo:
                            </p>
                            <button type="button" id="trocarCapaBtn" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-exchange-alt mr-1"></i>
                                Usar capa personalizada
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upload de Capa Personalizada (opcional, aparece se clicar) -->
            <div id="capaPersonalizadaContainer" class="mb-6 hidden">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-image mr-1"></i> Capa Personalizada
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                    <input 
                        type="file" 
                        name="capa_personalizada" 
                        accept="image/jpeg,image/jpg,image/png"
                        id="capaInput"
                        class="hidden"
                    >
                    <label for="capaInput" class="cursor-pointer">
                        <i class="fas fa-image text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">Clique para selecionar uma imagem</p>
                        <p class="text-xs text-gray-500 mt-2">JPG ou PNG (recomendado 300x400px)</p>
                        <div id="previewCapaPersonalizada" class="mt-4"></div>
                    </label>
                </div>
                <button type="button" id="voltarCapaPdfBtn" class="text-blue-600 hover:underline text-sm mt-2">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Voltar para capa do PDF
                </button>
            </div>

            <!-- Campo hidden para enviar capa gerada -->
            <input type="hidden" name="capa_gerada_base64" id="capaGeradaBase64">
            
            <!-- Título -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-heading mr-1"></i> Título
                </label>
                <input 
                    type="text" 
                    name="titulo" 
                    maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    placeholder="Deixe vazio para extrair automaticamente do PDF"
                >
                <p class="text-sm text-gray-500 mt-1">
                    Se deixar vazio, tentaremos extrair do PDF automaticamente
                </p>
            </div>
            
            <!-- Autor -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-user-edit mr-1"></i> Autor
                </label>
                <input 
                    type="text" 
                    name="autor" 
                    maxlength="255"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    placeholder="Nome do autor (opcional)"
                >
            </div>
            
            <!-- Descrição -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-align-left mr-1"></i> Descrição
                </label>
                <textarea 
                    name="descricao" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    placeholder="Uma breve descrição sobre o livro (opcional)"
                ></textarea>
            </div>
            
            <!-- Grid com 2 colunas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <!-- Coleção -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-folder mr-1"></i> Coleção
                    </label>
                    <select 
                        name="colecao_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    >
                        <option value="">Sem coleção</option>
                        <?php foreach ($colecoes as $col): ?>
                            <option value="<?= $col['id'] ?>"><?= htmlspecialchars($col['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-bookmark mr-1"></i> Status
                    </label>
                    <select 
                        name="status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    >
                        <option value="quer_ler">📚 Quero Ler</option>
                        <option value="lendo">📖 Lendo</option>
                        <option value="pausado">⏸️ Pausado</option>
                        <option value="concluido">✅ Concluído</option>
                    </select>
                </div>
                
            </div>
            
            <!-- Tags -->
            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-2">
                    <i class="fas fa-tags mr-1"></i> Tags
                </label>
                <input 
                    type="text" 
                    name="tags" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    placeholder="Ex: ficção, aventura, fantasia (separadas por vírgula)"
                >
            </div>
            
            <!-- Botões -->
            <div class="flex flex-col-reverse sm:flex-row justify-between items-center pt-6 border-t mt-8">
                <a href="/livros" class="text-gray-600 hover:text-gray-800 mt-4 sm:mt-0 w-full sm:w-auto text-center py-3 sm:py-0">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>
                
                <button 
                    type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-bold w-full sm:w-auto"
                >
                    <i class="fas fa-save mr-2"></i>
                    Adicionar Livro
                </button>
            </div>
            
        </form>
        
    </div>
    
</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
// Configuração do PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const pdfInput = document.getElementById('pdfInput');
const nomeArquivo = document.getElementById('nomeArquivo');
const processandoPdf = document.getElementById('processandoPdf');
const previewCapaContainer = document.getElementById('previewCapaContainer');
const previewCapaImg = document.getElementById('previewCapaImg');
const capaGeradaBase64 = document.getElementById('capaGeradaBase64');
const capaPersonalizadaContainer = document.getElementById('capaPersonalizadaContainer');
const trocarCapaBtn = document.getElementById('trocarCapaBtn');
const voltarCapaPdfBtn = document.getElementById('voltarCapaPdfBtn');

// Quando seleciona PDF
pdfInput.addEventListener('change', async function() {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const tamanhoMB = (file.size / 1024 / 1024).toFixed(2);
        nomeArquivo.textContent = `📄 ${file.name} (${tamanhoMB} MB)`;
        
        // Mostra loading
        processandoPdf.classList.remove('hidden');
        
        // Gera capa da primeira página
        await gerarCapaDoPdf(file);
        
        // Esconde loading
        processandoPdf.classList.add('hidden');
    }
});

// Função para gerar capa do PDF
async function gerarCapaDoPdf(file) {
    try {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({data: arrayBuffer}).promise;
        const page = await pdf.getPage(1); // Primeira página
        
        const viewport = page.getViewport({scale: 1.5});
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
        await page.render({
            canvasContext: context,
            viewport: viewport
        }).promise;
        
        // Converte para base64
        const capaBase64 = canvas.toDataURL('image/jpeg', 0.9);
        
        // Salva no campo hidden
        capaGeradaBase64.value = capaBase64;
        
        // Mostra preview
        previewCapaImg.src = capaBase64;
        previewCapaContainer.classList.remove('hidden');
        
    } catch (error) {
        console.error('Erro ao gerar capa:', error);
        alert('Não foi possível gerar capa automaticamente. Você pode fazer upload de uma capa personalizada.');
    }
}

// Trocar para capa personalizada
trocarCapaBtn.addEventListener('click', function() {
    capaPersonalizadaContainer.classList.remove('hidden');
    previewCapaContainer.classList.add('hidden');
});

// Voltar para capa do PDF
voltarCapaPdfBtn.addEventListener('click', function() {
    capaPersonalizadaContainer.classList.add('hidden');
    previewCapaContainer.classList.remove('hidden');
    document.getElementById('capaInput').value = '';
    document.getElementById('previewCapaPersonalizada').innerHTML = '';
});

// Preview da capa personalizada
const capaInput = document.getElementById('capaInput');
const previewCapaPersonalizada = document.getElementById('previewCapaPersonalizada');

capaInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewCapaPersonalizada.innerHTML = `
                <img src="${e.target.result}" class="max-w-xs mx-auto rounded shadow-lg">
                <p class="text-green-600 font-semibold mt-2">
                    <i class="fas fa-check-circle mr-1"></i>
                    Capa personalizada selecionada!
                </p>
            `;
            
            // Limpa a capa gerada para usar a personalizada
            capaGeradaBase64.value = '';
        };
        
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>