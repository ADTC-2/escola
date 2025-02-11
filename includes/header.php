<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escola Bíblica Dominical</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Font Awesome (ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="../assets/js/jquery-3.7.1.min.js"></script>
</head>
<body style="font-family: 'Roboto', sans-serif; background-color: #f4f4f9;">

<!-- Barra de Navegação -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../views/dashboard.php">
            <img src="../assets/img/logotipo.png" alt="Logo" width="60" height="50" class="d-inline-block align-top">
            <span class="ms-2 fw-bold">Escola Bíblica</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../views/alunos.php">Alunos</a></li>
                <li class="nav-item"><a class="nav-link" href="../views/classes.php">Classes</a></li>
                <li class="nav-item"><a class="nav-link" href="../views/chamada.php">Chamada</a></li>
                <li class="nav-item"><a class="nav-link" href="../views/relatorios.php">Relatórios</a></li>
                <li class="nav-item"><a class="nav-link" href="../views/perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="../controllers/autenticacao.php?logout=true"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Conteúdo Principal -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Bem-vindo à Escola Bíblica Dominical</h3>
                    <p class="card-text text-center">Aqui você pode gerenciar alunos, classes, chamadas e relatórios.</p>
                    <div class="d-flex justify-content-center mt-4">
                        <a href="../views/alunos.php" class="btn btn-primary btn-lg mx-2">Gerenciar Alunos</a>
                        <a href="../views/classes.php" class="btn btn-secondary btn-lg mx-2">Gerenciar Classes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




