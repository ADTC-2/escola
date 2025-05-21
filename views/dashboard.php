<?php  
require_once '../config/conexao.php';
require_once '../auth/valida_sessao.php';
require_once '../functions/funcoes_chamadas.php';

$estatisticas = obterEstatisticasChamadasMensais($pdo);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escola Bíblica</title>
  
  <!-- Bootstrap + Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/biblia.png" type="image/x-icon">

  <style>
    body {
      background: #fdfdfd;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
    padding-top: 68px; /* Ajuste esse valor conforme a altura da sua navbar */
  }
    .navbar {
      background: #ffffff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .navbar-brand {
      font-weight: bold;
      color: #4a90e2 !important;
    }

    .nav-link {
      font-weight: 500;
      color: #333 !important;
    }

    .nav-link:hover {
      color: #4a90e2 !important;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.2rem;
      margin-top: 10px;
    }
    .hero {
  background: url('../assets/images/fundo_ebd.jpg') center center / cover no-repeat;
  color: white;
  padding: 100px 0;
  text-align: center;
  position: relative;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5); /* escurece o fundo para dar contraste ao texto */
  z-index: 0;
}

.hero .container {
  position: relative;
  z-index: 1;
}
    .section {
      padding: 60px 0;
    }

    .section-title {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 40px;
      text-align: center;
      color: #333;
    }

    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.07);
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: scale(1.02);
    }

    .btn-primary {
      background-color: #ff416c;
      border: none;
      font-weight: bold;
    }

    .carousel .alert {
      font-size: 1.1rem;
      padding: 1.5rem;
      border-radius: 1rem;
    }

    footer {
      background-color: #f8f9fa;
      padding: 30px 0;
      text-align: center;
      color: #6c757d;
    }

    .profile-info p {
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<body>

<!-- Navbar Original -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../assets/images/biblia.png" alt="Logo EBD" style="height: 40px; margin-right: 10px;">
                <span>Escola Bíblica</span>
            </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="./alunos/index.php">Alunos</a></li>
        <li class="nav-item"><a class="nav-link" href="./classes/index.php">Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="./professores/index.php">Professores</a></li>
        <li class="nav-item"><a class="nav-link" href="./congregacao/index.php">Congregações</a></li>
        <li class="nav-item"><a class="nav-link" href="./matriculas/index.php">Matriculas</a></li>
        <li class="nav-item"><a class="nav-link" href="./usuario/index.php">Usuários</a></li>
        <li class="nav-item"><a class="nav-link active" href="./permissoes/index.php">Permissões</a></li>
        <li class="nav-item"><a class="nav-link" href="./relatorios/index.php">Relatórios</a></li>
        <li class="nav-item">
          <a class="nav-link" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i></a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<div class="hero">
  <div class="container">
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_perfil']); ?>!</h1>
    <p>Gerencie a Escola Bíblica Dominical com praticidade e organização.</p>
  </div>
</div>

<!-- Carrossel de Avisos -->
<div id="carouselAvisos" class="carousel slide bg-light py-4" data-bs-ride="carousel">
  <div class="carousel-inner container">
    <div class="carousel-item active">
      <div class="alert alert-info text-center shadow">📢 Sisitema em fase de testes.Versão Beta atualmente sendo executada!</div>
    </div> 
    <div class="carousel-item">
      <div class="alert alert-success text-center shadow">✅ Novo material disponível na seção de relatórios.</div>
    </div>
    <div class="carousel-item">
      <div class="alert alert-warning text-center shadow">⚠️ Registre as chamadas até domingo à noite!</div>
    </div>
  </div>
</div>

<!-- Cards -->
<section class="section">
  <div class="container">
    <h2 class="section-title">Painel Rápido</h2>
    <div class="row g-4">

      <!-- Chamadas -->
      <div class="col-md-4" data-aos="fade-up">
        <div class="card p-4 text-center">
          <i class="fas fa-book fa-2x mb-3 text-primary"></i>
          <h5>Chamadas</h5>
          <p>Registre a frequência das turmas e mantenha o histórico.</p>
          <a href="../views/chamadas/index.php" class="btn btn-primary mt-2">Nova Chamada</a>
          <a href="../views/chamadas/listar.php" class="btn btn-warning mt-2">Editar Chamada</a>
          <a href="../views/presencas/index.php" class="btn btn-info mt-2">Corrigir Presenças</a>
          <a href="./sorteios.php" class="btn btn-primary mt-2">
            <i class="fas fa-dice"></i> Sorteios
          </a>
        </div>
      </div>

      <!-- Perfil -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card p-4 text-center profile-info">
          <i class="fas fa-user-circle fa-2x mb-3 text-secondary"></i>
          <h5>Seu Perfil</h5>
          <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['usuario_email']); ?></p>
          <p><strong>Função:</strong> <?= htmlspecialchars($_SESSION['usuario_perfil']); ?></p>
        </div>
      </div>

      <!-- Últimas Chamadas -->
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card p-4">
          <h5 class="text-center mb-3">Últimas Chamadas</h5>
          <?php exibirUltimasChamadasPorClasse($pdo); ?>
        </div>
      </div>

    </div>
  </div>
</section>

<footer>
  <div class="container">
    <p>&copy; <?= date('Y') ?> Sistema E.B.D - Todos os direitos reservados.</p>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>

</body>
</html>