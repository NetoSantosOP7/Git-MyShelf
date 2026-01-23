<?php

$routes = [
    '' => 'HomeController@index',
    'login' => 'AuthController@login',
    'register' => 'AuthController@register',
    'logout' => 'AuthController@logout',

    'dashboard' => 'DashboardController@index',

    'colecoes' => 'ColecaoController@index',
    'colecoes/criar' => 'ColecaoController@create',
    'colecoes/ver' => 'ColecaoController@ver',
    'colecoes/salvar' => 'ColecaoController@store',
    'colecoes/editar' => 'ColecaoController@edit',
    'colecoes/atualizar' => 'ColecaoController@update',
    'colecoes/deletar' => 'ColecaoController@delete',

    'livros' => 'LivroController@index',
    'livros/upload' => 'LivroController@upload',
    'livros/salvar' => 'LivroController@store',
    'livros/editar' => 'LivroController@edit',
    'livros/atualizar' => 'LivroController@update',
    'livros/detalhes' => 'LivroController@detalhes',
    'livros/deletar' => 'LivroController@delete',

    'leitor' => 'LeitorController@index',
    'leitor/salvar-progresso' => 'LeitorController@salvarProgresso',

    'marcadores/adicionar' => 'MarcadorController@adicionar',
    'marcadores/listar' => 'MarcadorController@listar',
    'marcadores/deletar' => 'MarcadorController@deletar',

    'preferencias/dark-mode' => 'PreferenciasController@alternarDarkMode',
];

return $routes;
