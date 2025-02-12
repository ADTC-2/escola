<?php
session_start();
require_once "../config/conexao.php";

// Verifica se já existe um usuário logado (se a sessão está ativa)
if (isset($_SESSION["usuario_id"])) {
    // Verifica o perfil do usuário logado
    if ($_SESSION["usuario_perfil"] == "admin" || $_SESSION["usuario_perfil"] == "professor") {
        // Se for admin ou professor, redireciona para o dashboard
        header("Location: ../views/dashboard.php");
        exit();
    }
}

// Verifica se é uma requisição POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitiza os dados de entrada
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $senha = $_POST["senha"];  // Senha não precisa ser sanitizada

    // Verifica se o email está no formato correto
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Por favor, insira um email válido.";
        exit();
    }

    // Prepara a consulta para verificar o usuário
    $stmt = $pdo->prepare("SELECT id, nome, senha, perfil FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e compara a senha diretamente
    if ($usuario && $senha === $usuario["senha"]) {
        // Cria as variáveis de sessão com os dados do usuário
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["usuario_nome"] = $usuario["nome"];
        $_SESSION["usuario_perfil"] = $usuario["perfil"];
        
        // Verifica se o perfil é admin ou professor, e redireciona para o dashboard
        if ($_SESSION["usuario_perfil"] == "admin" || $_SESSION["usuario_perfil"] == "professor") {
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            echo "Credenciais válidas, mas você não tem permissão para acessar o dashboard.";
        }
    } else {
        // Mensagem de erro genérica
        echo "Credenciais inválidas. Tente novamente.";
    }
}
?>

