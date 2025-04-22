<?php
include('../../config/conexao.php');
include('../../views/includes/header.php');

// Filtros
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-d');
$congregacao_id = $_GET['congregacao_id'] ?? '';

// Consulta principal ajustada
$sql = "SELECT 
            a.id AS aluno_id,
            a.nome AS aluno_nome,
            c.nome AS classe_nome,
            m.trimestre,
            COUNT(CASE WHEN p.presente = 'presente' THEN 1 END) AS total_presencas,
            COUNT(CASE WHEN p.presente = 'ausente' OR p.presente = '' THEN 1 END) AS total_faltas,
            cg.nome AS congregacao_nome
        FROM presencas p
        JOIN chamadas ch ON ch.id = p.chamada_id
        JOIN alunos a ON a.id = p.aluno_id
        JOIN matriculas m ON m.aluno_id = a.id AND m.classe_id = ch.classe_id
        JOIN classes c ON c.id = ch.classe_id
        JOIN congregacoes cg ON cg.id = m.congregacao_id
        WHERE ch.data BETWEEN :data_inicio AND :data_fim
          AND m.status = 'ativo'";

if (!empty($congregacao_id)) {
    $sql .= " AND cg.id = :congregacao_id";
}

$sql .= " GROUP BY a.id, m.trimestre ORDER BY a.nome";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':data_inicio', $data_inicio);
$stmt->bindValue(':data_fim', $data_fim);
if (!empty($congregacao_id)) {
    $stmt->bindValue(':congregacao_id', $congregacao_id);
}
$stmt->execute();
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Congregações
$congs = $pdo->query("SELECT id, nome FROM congregacoes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Rankings
$top_presencas = $dados;
usort($top_presencas, fn($a, $b) => $b['total_presencas'] <=> $a['total_presencas']);
$top_presencas = array_slice($top_presencas, 0, 10);

$top_faltas = $dados;
usort($top_faltas, fn($a, $b) => $b['total_faltas'] <=> $a['total_faltas']);
$top_faltas = array_slice($top_faltas, 0, 10);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Presenças</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <style>
        .badge-presente {
            background-color: #28a745;
        }
        .badge-falta {
            background-color: #dc3545;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-4 px-3">
    <h4 class="mb-4 text-center">📊 Relatório Geral de Presenças por Trimestre</h4>

    <form class="row g-3 mb-4" method="GET">
        <div class="col-12 col-md-3">
            <label>Data Início:</label>
            <input type="date" name="data_inicio" class="form-control" value="<?= $data_inicio ?>">
        </div>
        <div class="col-12 col-md-3">
            <label>Data Fim:</label>
            <input type="date" name="data_fim" class="form-control" value="<?= $data_fim ?>">
        </div>
        <div class="col-12 col-md-4">
            <label>Congregação:</label>
            <select name="congregacao_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach($congs as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($congregacao_id == $c['id']) ? 'selected' : '' ?>><?= $c['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <label>&nbsp;</label>
            <button class="btn btn-primary" type="submit">Filtrar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table id="tabela" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead class="table-dark">
                <tr>
                    <th>Aluno</th>
                    <th>Classe</th>
                    <th>Congregação</th>
                    <th>Trimestre</th>
                    <th>Presenças</th>
                    <th>Faltas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dados as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['aluno_nome']) ?></td>
                        <td><?= htmlspecialchars($d['classe_nome']) ?></td>
                        <td><?= htmlspecialchars($d['congregacao_nome']) ?></td>
                        <td><?= $d['trimestre'] ?></td>
                        <td><span class="badge badge-presente"><?= $d['total_presencas'] ?></span></td>
                        <td><span class="badge badge-falta"><?= $d['total_faltas'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Rankings -->
    <div class="row mt-5">
        <div class="col-12 col-md-6 mb-4">
            <h5 class="mb-3 text-success">🎯 Top 10 com Mais Presenças</h5>
            <ul class="list-group">
                <?php foreach($top_presencas as $p): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center small">
                        <?= htmlspecialchars($p['aluno_nome']) ?> (<?= htmlspecialchars($p['classe_nome']) ?>)
                        <span class="badge bg-success rounded-pill"><?= $p['total_presencas'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-12 col-md-6 mb-4">
            <h5 class="mb-3 text-danger">⚠️ Top 10 com Mais Faltas</h5>
            <ul class="list-group">
                <?php foreach($top_faltas as $f): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center small">
                        <?= htmlspecialchars($f['aluno_nome']) ?> (<?= htmlspecialchars($f['classe_nome']) ?>)
                        <span class="badge bg-danger rounded-pill"><?= $f['total_faltas'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        $('#tabela').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            },
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print']
        });
    });
</script>

</body>
</html>













