<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Bem-vindo, <?php echo $_SESSION["usuario_nome"]; ?>!</h3>
        <a href="../auth/logout.php" class="btn btn-danger">Sair</a>
    </div>
</body>
</html>
