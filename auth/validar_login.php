<?php
session_start();
require_once "../config/conexao.php"; // Certifique-se de que a conexão está correta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $_SESSION['mensagem'] = "Preencha todos os campos.";
        header("Location: login.php");
        exit();
    }

    try {
        // Busca o usuário pelo email
        $sql = "SELECT id, email, senha FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $usuario['id'];
            $email_db = $usuario['email'];
            $senha_db = $usuario['senha']; // Senha armazenada no banco

            // Comparação direta da senha (NÃO SEGURO PARA PRODUÇÃO)
            if ($senha === $senha_db) {
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_email'] = $email_db;
                header("Location: ../views/dashboard.php");
                exit();
            } else {
                $_SESSION['mensagem'] = "Senha incorreta.";
            }
        } else {
            $_SESSION['mensagem'] = "Usuário não encontrado.";
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "Erro no banco de dados: " . $e->getMessage();
    }

    header("Location: ../auth/login.php");
    exit();
}

$_SESSION['mensagem'] = "Requisição inválida.";
header("Location: ../auth/login.php");
exit();
?>















