<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Escola Bíblica</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="alunos.php">Alunos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chamada.php">Chamada</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="relatorios.php">Relatórios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../controllers/autenticacao.php?logout=true">Logout</a>
            </li>
        </ul>
    </div>
</nav>

