<?php
ob_start(); // Inicia o buffer de saída

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/Usuario.php';
require_once '../config/conexao.php';

$usuario = new Usuario($pdo);

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['senha'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha']; // A senha será verificada com password_verify()

    $usuario_info = $usuario->autenticar($email, $senha);

    if ($usuario_info) {
        $_SESSION['usuario_id'] = $usuario_info['id'];
        $_SESSION['usuario_nome'] = $usuario_info['nome'];
        $_SESSION['usuario_email'] = $usuario_info['email'];
        $_SESSION['usuario_perfil'] = $usuario_info['perfil'];
        
        header("Location: ../views/dashboard.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Usuário ou senha inválidos!";
        header("Location: ../views/login.php");
        exit();
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../views/login.php");
    exit();
}

ob_end_flush(); // Libera o buffer de saída
?>













