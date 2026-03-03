# MyShelf - Biblioteca Digital

Sistema de gerenciamento de biblioteca digital pessoal desenvolvido em PHP puro, permitindo upload, organização e leitura de livros em PDF com sistema de marcadores e acompanhamento de progresso.

 **[Acesse a aplicação](https://myshelf-2q9l.onrender.com)**

## Sobre o Projeto

MyShelf é uma aplicação web para gerenciar sua biblioteca digital pessoal. Permite fazer upload de livros em PDF, organizá-los em coleções personalizadas, ler diretamente no navegador e acompanhar seu progresso de leitura.

## Funcionalidades Principais

- Sistema completo de autenticação (registro/login)
- Upload e gerenciamento de livros em PDF
- Extração automática de metadados (páginas, tamanho)
- Organização em coleções personalizadas com cores e ícones
- Leitor de PDF integrado com navegação
- Sistema de marcadores por página
- Acompanhamento de progresso de leitura
- Status de leitura (Quer Ler, Lendo, Concluído)
- Dashboard com estatísticas
- Modo escuro/claro

## Tecnologias

- **PHP 8.2** com PDO
- **MySQL** - Banco de dados
- **Apache** - Servidor web
- **Docker** - Containerização
- **smalot/pdfparser** - Extração de metadados de PDFs
- **PDF.js** - Renderização de PDFs no navegador
- **vlucas/phpdotenv** - Gerenciamento de variáveis de ambiente

## Estrutura do Projeto

```
myshelf/
├── app/
│   ├── Controllers/          # Controladores da aplicação
│   │   ├── AuthController.php
│   │   ├── ColecaoController.php
│   │   ├── DashboardController.php
│   │   ├── LeitorController.php
│   │   ├── LivroController.php
│   │   ├── MarcadorController.php
│   │   └── PreferenciasController.php
│   ├── Core/
│   │   └── Router.php        # Sistema de roteamento
│   ├── Helpers/
│   │   ├── PdfHelper.php     # Manipulação de PDFs
│   │   └── Preferencias.php  # Gerenciamento de preferências
│   ├── Models/               # Modelos de dados
│   │   ├── Database.php      # Singleton de conexão
│   │   ├── Usuario.php
│   │   ├── Livro.php
│   │   ├── Colecao.php
│   │   └── Marcador.php
│   └── Views/                # Templates PHP
│       ├── auth/
│       ├── colecoes/
│       ├── layouts/
│       ├── leitor/
│       └── livros/
├── config/
│   └── database.php          # Configuração do banco
├── public/                   # DocumentRoot
│   ├── index.php            # Ponto de entrada
│   └── uploads/
│       ├── capas/
│       └── pdfs/
└── routes/
    └── web.php              # Definição de rotas
```

## Como Funciona

### Fluxo de Requisição
```
Requisição → public/index.php → Router::handle() → Controller@method → View
```

Todas as requisições passam pelo `public/index.php`, que carrega as dependências e repassa para o Router. O Router mapeia a URL para o controller e método correspondente.

### Sistema de Autenticação
```
Login → Usuario::login() → Validação de credenciais → Hash bcrypt → Sessão → Redirecionamento
```

Senhas são criptografadas com bcrypt. As sessões armazenam ID, nome e email do usuário.

### Upload e Processamento de Livros
```
Upload → Validação → PdfHelper::extrairMetadados() → Livro::criar() → Salvamento no DB
```

O sistema extrai automaticamente número de páginas e tamanho do arquivo. PDFs e capas são salvos em `public/uploads/`.

### Leitor de PDF
```
Leitor → PDF.js carrega arquivo → Navegação entre páginas → Auto-save de progresso
```

O progresso é salvo automaticamente via AJAX. Calcula porcentagem lida e atualiza status do livro.

##  Arquitetura

### Padrão MVC

**Models** (`app/Models/`)
- Lógica de negócio e acesso a dados
- Cada model representa uma entidade (Usuario, Livro, Colecao, Marcador)
- Utilizam PDO com prepared statements

**Views** (`app/Views/`)
- Templates PHP para renderização
- Organizadas por funcionalidade (auth, livros, colecoes, leitor)
- Layouts compartilhados

**Controllers** (`app/Controllers/`)
- Intermediam Models e Views
- Processam requisições e respostas
- Validam permissões e sessões

### Padrões Implementados

**Singleton** - Classe `Database` garante uma única conexão com o banco

**Repository Pattern** - Models funcionam como repositórios de dados

**Front Controller** - `Router` centraliza todas as requisições

### Segurança

- Prepared Statements (PDO) previnem SQL Injection
- Bcrypt para hash de senhas
- Validação de sessões em rotas protegidas
- Verificação de propriedade de recursos (usuário só acessa seus próprios dados)
- Sanitização de inputs do usuário

## Rotas da Aplicação

### Públicas
- `GET /` - Página inicial
- `GET /login` - Login
- `GET /register` - Registro

### Autenticadas
- `GET /dashboard` - Dashboard com estatísticas
- `GET /logout` - Logout

### Livros
- `GET /livros` - Biblioteca completa
- `GET /livros/upload` - Upload de novo livro
- `POST /livros/salvar` - Processar upload
- `GET /livros/detalhes?id={id}` - Detalhes do livro
- `GET /livros/editar?id={id}` - Editar informações
- `POST /livros/atualizar` - Salvar edição
- `POST /livros/deletar` - Remover livro

### Coleções
- `GET /colecoes` - Listar coleções
- `GET /colecoes/criar` - Nova coleção
- `POST /colecoes/salvar` - Criar coleção
- `GET /colecoes/ver?id={id}` - Visualizar coleção
- `GET /colecoes/editar?id={id}` - Editar coleção
- `POST /colecoes/atualizar` - Salvar edição
- `POST /colecoes/deletar` - Remover coleção

### Leitor
- `GET /leitor?id={id}` - Abrir leitor de PDF
- `POST /leitor/salvar-progresso` - Salvar página atual (AJAX)

### Marcadores
- `POST /marcadores/adicionar` - Criar marcador (AJAX)
- `GET /marcadores/listar?livro_id={id}` - Listar marcadores (AJAX)
- `POST /marcadores/deletar` - Remover marcador (AJAX)

### Preferências
- `POST /preferencias/dark-mode` - Toggle dark mode (AJAX)

## Banco de Dados

### Estrutura

**usuarios** - Dados dos usuários cadastrados
- Armazena nome, email e senha (hash bcrypt)

**colecoes** - Coleções personalizadas
- Cada usuário pode criar múltiplas coleções
- Possui nome, descrição, cor e ícone customizáveis

**livros** - Biblioteca de PDFs
- Vinculado a usuário e opcionalmente a uma coleção
- Armazena metadados (título, autor, descrição, tags)
- Controla progresso (página atual, porcentagem lida)
- Status: quer_ler, lendo, concluido
- Paths para arquivo PDF e capa

**marcadores** - Bookmarks por página
- Vinculado a livro e usuário
- Armazena número da página e anotação opcional

### Relacionamentos

```
usuarios (1) ──→ (N) colecoes
usuarios (1) ──→ (N) livros
usuarios (1) ──→ (N) marcadores
colecoes (1) ──→ (N) livros
livros (1) ──→ (N) marcadores
```

## Segurança Implementada

- Prepared Statements em todas as queries
- Bcrypt para hash de senhas
- Validação de sessão em rotas protegidas
- Verificação de propriedade (usuário só acessa seus dados)
- Sanitização de inputs
- Proteção contra SQL Injection
- Deleção em cascata no banco

## Detalhes Técnicos

### Autoload
Composer gerencia autoload das classes via `classmap` para Models, Controllers e Core.

### Roteamento
Sistema simples baseado em array associativo (`routes/web.php`). Mapeia URL → Controller@method.

### Conexão com Banco
Singleton pattern garante uma única instância PDO. Suporta SSL para conexões remotas (Aiven Cloud).

### Upload de Arquivos
PDFs e capas são salvos com nomes únicos (uniqid) em `public/uploads/`. Metadados extraídos via smalot/pdfparser.

### Progresso de Leitura
Salvo via AJAX a cada mudança de página. Calcula porcentagem automaticamente e atualiza status do livro.

---
