<?php
// Incluir o arquivo de conexão
require_once 'config/conexao.php';

// Iniciar a sessão, se necessário
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: views/login.php");
    exit(); // Certifique-se de que a execução do código pare após o redirecionamento
}








