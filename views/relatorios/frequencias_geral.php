<?php
require_once('../../config/conexao.php');
require_once('../../views/includes/header.php');

// Filtros
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-d');
$congregacao_id = $_GET['congregacao_id'] ?? '';
$trimestre = $_GET['trimestre'] ?? '';
$classe_id = $_GET['classe_id'] ?? '';

// Query ajustada com a lógica correta de contagem
$sql = "SELECT 
            a.id AS aluno_id,
            a.nome AS aluno_nome,
            c.nome AS classe_nome,
            cg.nome AS congregacao_nome,
            m.trimestre,
            DATE_FORMAT(ch.data, '%Y-%m') AS mes,
            COUNT(DISTINCT CASE WHEN p.presente = 'presente' THEN p.id END) AS total_presencas,
            COUNT(DISTINCT CASE WHEN p.presente = 'ausente' OR p.presente = '' THEN p.id END) AS total_faltas,
            COUNT(DISTINCT CASE WHEN p.presente IS NOT NULL THEN p.id END) AS total_registros
        FROM alunos a
        JOIN matriculas m ON m.aluno_id = a.id
        JOIN classes c ON c.id = m.classe_id
        JOIN congregacoes cg ON cg.id = m.congregacao_id
        LEFT JOIN presencas p ON p.aluno_id = a.id
        LEFT JOIN chamadas ch ON ch.id = p.chamada_id AND ch.classe_id = m.classe_id
        WHERE m.status = 'ativo'";

if (!empty($data_inicio)) {
    $sql .= " AND ch.data BETWEEN :data_inicio AND :data_fim";
}
if (!empty($congregacao_id)) {
    $sql .= " AND m.congregacao_id = :congregacao_id";
}
if (!empty($trimestre)) {
    $sql .= " AND m.trimestre = :trimestre";
}
if (!empty($classe_id)) {
    $sql .= " AND m.classe_id = :classe_id";
}

$sql .= " GROUP BY a.id, m.trimestre, mes ORDER BY a.nome, m.trimestre, mes";

$stmt = $pdo->prepare($sql);
if (!empty($data_inicio)) {
    $stmt->bindParam(':data_inicio', $data_inicio);
    $stmt->bindParam(':data_fim', $data_fim);
}
if (!empty($congregacao_id)) {
    $stmt->bindParam(':congregacao_id', $congregacao_id);
}
if (!empty($trimestre)) {
    $stmt->bindParam(':trimestre', $trimestre);
}
if (!empty($classe_id)) {
    $stmt->bindParam(':classe_id', $classe_id);
}
$stmt->execute();
$relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Congregações para o filtro
$congs = $pdo->query("SELECT id, nome FROM congregacoes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Classes para o filtro
$classes = $pdo->query("SELECT id, nome FROM classes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Alunos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables + Buttons -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .table thead th {
            vertical-align: middle;
            white-space: nowrap;
        }
        .table tbody td {
            vertical-align: middle;
        }
        .badge-presente {
            background-color: #28a745;
        }
        .badge-falta {
            background-color: #dc3545;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-4">
    <h3 class="text-center mb-4">📊 Relatório de Presenças por Aluno</h3>

    <!-- Filtros -->
    <form class="row g-2 mb-4" method="get">
        <div class="col-md-2">
            <label for="data_inicio" class="form-label">Data Início</label>
            <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= $data_inicio ?>">
        </div>
        <div class="col-md-2">
            <label for="data_fim" class="form-label">Data Fim</label>
            <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= $data_fim ?>">
        </div>
        <div class="col-md-2">
            <label for="congregacao_id" class="form-label">Congregação</label>
            <select name="congregacao_id" id="congregacao_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($congs as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($congregacao_id == $c['id']) ? 'selected' : '' ?>>
                        <?= $c['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="classe_id" class="form-label">Classe</label>
            <select name="classe_id" id="classe_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($classes as $cl): ?>
                    <option value="<?= $cl['id'] ?>" <?= ($classe_id == $cl['id']) ? 'selected' : '' ?>>
                        <?= $cl['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="trimestre" class="form-label">Trimestre</label>
            <select name="trimestre" id="trimestre" class="form-select">
                <option value="">Todos</option>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <option value="<?= $i ?>" <?= ($trimestre == $i) ? 'selected' : '' ?>><?= $i ?>º</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <!-- Tabela -->
    <div class="table-responsive">
        <table id="tabela" class="table table-bordered table-striped table-hover nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Aluno</th>
                    <th>Classe</th>
                    <th>Congregação</th>
                    <th>Trimestre</th>
                    <th>Mês</th>
                    <th>Presentes</th>
                    <th>Faltas</th>
                    <th>Total Registros</th>
                    <th>Frequência</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_geral_presencas = 0;
                $total_geral_faltas = 0;
                $total_geral_registros = 0;

                foreach ($relatorios as $row):
                    $total_registros = $row['total_registros'] ?? 0;
                    $frequencia = ($total_registros > 0) ? round(($row['total_presencas'] / $total_registros * 100), 2) : 0;
                    
                    $total_geral_presencas += $row['total_presencas'];
                    $total_geral_faltas += $row['total_faltas'];
                    $total_geral_registros += $total_registros;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['aluno_nome']) ?></td>
                    <td><?= htmlspecialchars($row['classe_nome']) ?></td>
                    <td><?= htmlspecialchars($row['congregacao_nome']) ?></td>
                    <td class="text-center"><?= $row['trimestre'] ?>º</td>
                    <td class="text-center">
                        <?= date('m/Y', strtotime($row['mes'] . '-01')) ?>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-presente"><?= $row['total_presencas'] ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-falta"><?= $row['total_faltas'] ?></span>
                    </td>
                    <td class="text-center"><?= $total_registros ?></td>
                    <td class="text-center"><?= $frequencia ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="5" class="text-end">Totais Gerais:</td>
                    <td class="text-center"><?= $total_geral_presencas ?></td>
                    <td class="text-center"><?= $total_geral_faltas ?></td>
                    <td class="text-center"><?= $total_geral_registros ?></td>
                    <td class="text-center">
                        <?= ($total_geral_registros > 0) ? round(($total_geral_presencas / $total_geral_registros * 100), 2) . '%' : '0%' ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables + Buttons -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tabela').DataTable({
            responsive: true,
            scrollX: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            },
            dom: 'Bfrtip',
            buttons: [               
                { 
                    extend: 'excelHtml5',
                    className: 'btn btn-success btn-sm',
                    text: '📊 Excel',
                    title: 'Relatório de Presenças por Aluno',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                { 
                    extend: 'pdfHtml5', 
                    className: 'btn btn-danger btn-sm', 
                    text: '📄 PDF',
                    title: 'Relatório de Presenças por Aluno',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                { 
                    extend: 'print', 
                    className: 'btn btn-secondary btn-sm', 
                    text: '🖨️ Imprimir',
                    title: 'Relatório de Presenças por Aluno',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            columnDefs: [
                { targets: [0, 1, 2], orderable: true },
                { targets: '_all', orderable: false }
            ],
            order: [[0, 'asc'], [3, 'asc'], [4, 'asc']] // Ordena por aluno, trimestre e mês
        });
    });
</script>

</body>
</html>