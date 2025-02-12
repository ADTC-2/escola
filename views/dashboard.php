<?php  require_once '../auth/valida_sessao.php'?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema E.B.D</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Bem-vindo, <?= $_SESSION['usuario_email']; ?>!</h2>
        <a href="../auth/logout.php" class="btn btn-danger">Sair</a>
    </div>
</body>
</html>

