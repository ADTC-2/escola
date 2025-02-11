<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

// Consultas para presenças e faltas por trimestre
$stmt = $pdo->query("SELECT 
                        QUARTER(data_chamada) AS trimestre,
                        YEAR(data_chamada) AS ano,
                        COUNT(CASE WHEN presente = 1 THEN 1 END) AS total_presentes,
                        COUNT(CASE WHEN presente = 0 THEN 1 END) AS total_faltaram
                    FROM chamada_alunos
                    GROUP BY YEAR(data_chamada), QUARTER(data_chamada)
                    ORDER BY YEAR(data_chamada), QUARTER(data_chamada)");

$dados_trimestrais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Arrays para armazenar os dados do gráfico
$trimestres = [];
$presentes = [];
$faltaram = [];

foreach ($dados_trimestrais as $linha) {
    $trimestres[] = 'T' . $linha['trimestre'] . ' - ' . $linha['ano'];
    $presentes[] = $linha['total_presentes'];
    $faltaram[] = $linha['total_faltaram'];
}

// Consultas para totais
$stmt = $pdo->query("SELECT COUNT(*) AS total_alunos FROM alunos");
$total_alunos = $stmt->fetch(PDO::FETCH_ASSOC)['total_alunos'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_classes FROM classes");
$total_classes = $stmt->fetch(PDO::FETCH_ASSOC)['total_classes'];

$stmt = $pdo->query("SELECT 
                        COUNT(CASE WHEN presente = 1 THEN 1 END) AS total_presentes,
                        COUNT(CASE WHEN presente = 0 THEN 1 END) AS total_faltaram
                    FROM chamada_alunos");
$chamadas = $stmt->fetch(PDO::FETCH_ASSOC);
$total_presentes = $chamadas['total_presentes'];
$total_faltaram = $chamadas['total_faltaram'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_usuarios FROM usuarios");
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios'];
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Painel de Controle</h2>

    <div class="row">
        <!-- Total de Alunos -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-primary d-flex flex-column" style="min-height: 250px;">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Total de Alunos</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="display-4"><?= $total_alunos; ?></h3>
                    <a href="alunos.php" class="btn btn-primary btn-block">Ver Alunos</a>
                </div>
            </div>
        </div>

        <!-- Total de Classes -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-success d-flex flex-column" style="min-height: 250px;">
                <div class="card-header bg-success text-white text-center">
                    <h5 class="mb-0">Total de Classes</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="display-4"><?= $total_classes; ?></h3>
                    <a href="classes.php" class="btn btn-success btn-block">Ver Classes</a>
                </div>
            </div>
        </div>

        <!-- Total de Chamadas (Presença e Falta) -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-warning d-flex flex-column" style="min-height: 250px;">
                <div class="card-header bg-warning text-dark text-center">
                    <h6 class="mb-0">Total de Chamadas</h6>
                </div>
                <div class="card-body text-center">
                    <p class="h6"><?= $total_presentes; ?> Presentes</p>
                    <p class="h6"><?= $total_faltaram; ?> Faltaram</p>
                    <a href="chamada.php" class="btn btn-warning btn-sm">Ver Chamadas</a>
                </div>
            </div>
        </div>

        <!-- Total de Usuários -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card shadow-sm border-info d-flex flex-column" style="min-height: 250px;">
                <div class="card-header bg-info text-white text-center">
                    <h5 class="mb-0">Total de Usuários</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="display-4"><?= $total_usuarios; ?></h3>
                    <a href="usuarios.php" class="btn btn-info btn-block">Ver Usuários</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Presenças e Faltas por Trimestre -->
    <h3 class="text-center mb-4">Presenças e Faltas por Trimestre</h3>

    <div class="row">
        <div class="col-12">
            <canvas id="chartTrimestre" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Inclusão do Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('chartTrimestre').getContext('2d');
    var chartTrimestre = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($trimestres); ?>,
            datasets: [{
                label: 'Presentes',
                data: <?php echo json_encode($presentes); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            {
                label: 'Faltaram',
                data: <?php echo json_encode($faltaram); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>



