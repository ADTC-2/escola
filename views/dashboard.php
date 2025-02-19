<?php  
    require_once '../config/conexao.php';
    require_once '../auth/valida_sessao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <!-- Ícones -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">E.B.D - Painel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="./alunos/index.php">Alunos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../classes/index.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="./professores/index.php">Professores</a></li>
                    <li class="nav-item"><a class="nav-link" href="./congregacao/index.php">Congregações</a></li>
                    <li class="nav-item"><a class="nav-link" href="./matriculas/index.php">Matriculas</a></li>
                    <li class="nav-item"><a class="nav-link" href="./usuario/index.php">Usuários</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Relatórios</a></li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Bem-vindo, <?= $_SESSION['usuario_perfil']; ?>!</h2>

        <div class="row">
            <!-- Card de Chamadas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <img src="../assets/images/user_img.png" alt="User">
                            <strong><?= $_SESSION['usuario_perfil']; ?></strong>
                        </div>
                        <div>
                            <i class="fas fa-cogs"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Total de Chamadas:</strong> 0</p>
                        <?php date_default_timezone_set('America/Sao_Paulo'); ?>
                        <p><strong>Última Chamada:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                        <a href="../views/chamadas/index.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Adicionar Chamada
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card de Usuário -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <img src="../assets/images/user_img.png" alt="User">
                            <strong>Seu Perfil</strong>
                        </div>
                        <div>
                            <i class="fas fa-cogs"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?= $_SESSION['usuario_email']; ?></p>
                        <p><strong>Função:</strong> <?= $_SESSION['usuario_perfil']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




