<?php 
$titulo = 'Biblioteca Digital - Organize seus PDFs';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .feature-card {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
        
        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .feature-card .icon-wrapper {
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }
        
        .hero-card {
            transition: transform 0.3s ease;
        }
        
        .hero-card:hover {
            transform: scale(1.02);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="min-h-screen bg-gradient-to-br from-blue-600 via-blue-700 to-purple-800 text-white">
    
    <nav class="container mx-auto px-4 py-6 animate-fade-in">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2 animate-float">
                <i class="fas fa-book-reader text-3xl"></i>
                <span class="text-2xl font-bold">Biblioteca Digital</span>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="/login" class="nav-link px-6 py-2 border-2 border-white rounded-lg hover:bg-white hover:text-blue-600 transition">
                    Entrar
                </a>
                <a href="/register" class="btn-primary px-6 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-semibold">
                    Criar Conta
                </a>
            </div>
            <div class="md:hidden">
                <button id="home-mobile-menu-button" class="text-white focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <div id="home-mobile-menu" class="hidden md:hidden mt-4">
            <a href="/login" class="block py-2 px-4 text-center border-2 border-white rounded-lg hover:bg-white hover:text-blue-600 transition">Entrar</a>
            <a href="/register" class="block mt-2 py-2 px-4 text-center bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-semibold">Criar Conta</a>
        </div>
    </nav>
    
    <div class="container mx-auto px-4 py-12 md:py-20">
        <div class="max-w-4xl mx-auto text-center">
            
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
                Sua Biblioteca Pessoal de PDFs
            </h1>
            
            <p class="text-lg md:text-2xl mb-8 text-blue-100 animate-fade-in-up" style="animation-delay: 0.4s; opacity: 0;">
                Organize, leia e acompanhe seu progresso em todos os seus livros digitais em um só lugar
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center sm:space-x-4 space-y-4 sm:space-y-0 mb-12 animate-fade-in-up" style="animation-delay: 0.6s; opacity: 0;">
                <a href="/register" class="btn-primary px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg">
                    Começar Agora
                </a>
                <a href="/login" class="btn-primary px-8 py-4 border-2 border-white rounded-lg hover:bg-white hover:bg-opacity-10 transition font-bold text-lg">
                    Já Tenho Conta
                </a>
            </div>
            
            <div class="hero-card bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-6 md:p-8 shadow-2xl animate-fade-in-up" style="animation-delay: 0.8s; opacity: 0;">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="hero-card bg-white bg-opacity-20 rounded-lg p-6 text-center">
                        <i class="fas fa-upload text-4xl md:text-5xl mb-4"></i>
                        <h3 class="font-bold text-lg mb-2">Upload Fácil</h3>
                        <p class="text-sm text-blue-100">Faça upload de PDFs e organizamos automaticamente</p>
                    </div>
                    <div class="hero-card bg-white bg-opacity-20 rounded-lg p-6 text-center">
                        <i class="fas fa-folder-open text-4xl md:text-5xl mb-4"></i>
                        <h3 class="font-bold text-lg mb-2">Coleções</h3>
                        <p class="text-sm text-blue-100">Organize em coleções personalizadas</p>
                    </div>
                    <div class="hero-card bg-white bg-opacity-20 rounded-lg p-6 text-center">
                        <i class="fas fa-book-reader text-4xl md:text-5xl mb-4"></i>
                        <h3 class="font-bold text-lg mb-2">Leitor Integrado</h3>
                        <p class="text-sm text-blue-100">Leia direto no navegador com progresso salvo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-20">
    <div class="container mx-auto px-4">
        
        <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-16 animate-fade-in-up">
            Recursos Principais
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="features-grid">
            
            <div class="feature-card bg-white rounded-lg shadow-lg p-8">
                <div class="icon-wrapper w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-file-pdf text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Extração Automática</h3>
                <p class="text-gray-600">
                    Extraímos automaticamente título, autor e número de páginas dos seus PDFs
                </p>
            </div>
            
            <div class="feature-card bg-white rounded-lg shadow-lg p-8">
                <div class="icon-wrapper w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-image text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Capas Geradas</h3>
                <p class="text-gray-600">
                    Capas geradas automaticamente ou faça upload da sua própria
                </p>
            </div>
            
            <div class="feature-card bg-white rounded-lg shadow-lg p-8">
                <div class="icon-wrapper w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-bookmark text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Acompanhe Progresso</h3>
                <p class="text-gray-600">
                    Continue de onde parou, com salvamento automático de página
                </p>
            </div>
            
            <div class="feature-card bg-white rounded-lg shadow-lg p-8">
                <div class="icon-wrapper w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-tags text-3xl text-yellow-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Tags e Filtros</h3>
                <p class="text-gray-600">
                    Adicione tags personalizadas e organize do seu jeito
                </p>
            </div>
            
            <div class="feature-card bg-white rounded-lg shadow-lg p-8">
                <div class="icon-wrapper w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Estatísticas</h3>
                <p class="text-gray-600">
                    Veja seu progresso, livros lidos e muito mais
                </p>
            </div>
            
            <div class="feature-card bg-white rounded-lg shadow-lg p-8">
                <div class="icon-wrapper w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-lock text-3xl text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-4 text-gray-800">Privacidade Total</h3>
                <p class="text-gray-600">
                    Seus PDFs ficam no seu servidor, 100% privado e seguro
                </p>
            </div>
        </div>
    </div>
</div>

<div class="bg-blue-600 text-white py-20">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6 animate-fade-in-up">
            Pronto para Organizar sua Biblioteca?
        </h2>
        <p class="text-lg md:text-xl mb-8 text-blue-100 animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
            Crie sua conta grátis e comece agora mesmo
        </p>
        <a href="/register" class="btn-primary inline-block px-10 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg animate-fade-in-up" style="animation-delay: 0.4s; opacity: 0;">
            <i class="fas fa-user-plus mr-2"></i>
            Criar Conta Grátis
        </a>
    </div>
</div>

<footer class="bg-gray-800 text-gray-400 py-8">
    <div class="container mx-auto px-4 text-center">
        <p>&copy; 2026 Biblioteca Digital. Feito por NetoSan.</p>
    </div>
</footer>

<script>
// Mobile menu toggle
document.getElementById('home-mobile-menu-button')?.addEventListener('click', function() {
    const mobileMenu = document.getElementById('home-mobile-menu');
    mobileMenu.classList.toggle('hidden');
});

// Intersection Observer para animar cards quando aparecem na tela
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 100);
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observar todos os cards de recursos
document.addEventListener('DOMContentLoaded', () => {
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => observer.observe(card));
});

// Smooth scroll para links internos
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

</body>
</html>