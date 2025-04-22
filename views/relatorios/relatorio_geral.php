<?php
require_once('../../config/conexao.php');
require_once('../../views/includes/header.php');

// Filtros
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-d');
$congregacao_id = $_GET['congregacao_id'] ?? '';
$trimestre = $_GET['trimestre'] ?? '';

// Query principal ajustada
$sql = "SELECT 
    c.id AS classe_id,
    c.nome AS classe_nome,
    cg.nome AS congregacao_nome,
    m.trimestre,
    COUNT(DISTINCT m.aluno_id) AS total_matriculados,
    SUM(CASE WHEN p.presente = 'presente' THEN 1 ELSE 0 END) AS total_presencas,
    SUM(CASE WHEN p.presente = 'ausente' OR p.presente = '' THEN 1 ELSE 0 END) AS total_faltas,
    COALESCE(SUM(DISTINCT ch.total_visitantes), 0) AS total_visitantes,
    COALESCE(SUM(DISTINCT ch.total_biblias), 0) AS total_biblias,
    COALESCE(SUM(DISTINCT ch.total_revistas), 0) AS total_revistas,
    COALESCE(SUM(DISTINCT CAST(ch.oferta_classe AS DECIMAL(10,2))), 0) AS total_ofertas
FROM classes c
LEFT JOIN matriculas m ON m.classe_id = c.id AND m.status = 'ativo'
LEFT JOIN congregacoes cg ON cg.id = m.congregacao_id
LEFT JOIN chamadas ch ON ch.classe_id = c.id AND ch.data BETWEEN :data_inicio AND :data_fim
LEFT JOIN presencas p ON p.chamada_id = ch.id AND p.aluno_id = m.aluno_id
WHERE 1=1";

if (!empty($congregacao_id)) {
    $sql .= " AND m.congregacao_id = :congregacao_id";
}
if (!empty($trimestre)) {
    $sql .= " AND m.trimestre = :trimestre";
}

$sql .= " GROUP BY c.id, c.nome, cg.nome, m.trimestre ORDER BY c.nome";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':data_inicio', $data_inicio);
$stmt->bindParam(':data_fim', $data_fim);
if (!empty($congregacao_id)) {
    $stmt->bindParam(':congregacao_id', $congregacao_id);
}
if (!empty($trimestre)) {
    $stmt->bindParam(':trimestre', $trimestre);
}
$stmt->execute();
$relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Congregações
$congs = $pdo->query("SELECT id, nome FROM congregacoes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório Geral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables + Buttons -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
    .dataTables_wrapper .row {
        width: 100% !important;
    }

    table.dataTable {
        width: 100% !important;
    }

    .container {
        max-width: 100% !important;
    }

    .table-responsive {
        overflow-x: visible !important;
    }
    </style>
</head>

<body class="bg-light">

    <div class="container my-4">
        <h3 class="text-center mb-4">📊 Relatório Geral de Presenças</h3>

        <!-- Filtros -->
        <form class="row g-2 mb-4" method="get">
            <div class="col-md-3">
                <label for="data_inicio" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= $data_inicio ?>">
            </div>
            <div class="col-md-3">
                <label for="data_fim" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= $data_fim ?>">
            </div>
            <div class="col-md-3">
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
                <label for="trimestre" class="form-label">Trimestre</label>
                <select name="trimestre" id="trimestre" class="form-select">
                    <option value="">Todos</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <option value="<?= $i ?>" <?= ($trimestre == $i) ? 'selected' : '' ?>><?= $i ?>º</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        <!-- Tabela -->
        <div class="table-responsive">
            <table id="tabela" class="table table-bordered table-striped table-hover nowrap w-100">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Classe</th>
                        <th>Congregação</th>
                        <th>Trimestre</th>
                        <th>Matriculados</th>
                        <th>Presentes</th>
                        <th>Faltas</th>
                        <th>Visitantes</th>
                        <th>Bíblias</th>
                        <th>Revistas</th>
                        <th>Ofertas (R$)</th>
                        <th>Frequência</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                $total_geral_matriculados = 0;
                $total_geral_presencas = 0;
                $total_geral_faltas = 0;
                $total_geral_visitantes = 0;
                $total_geral_biblias = 0;
                $total_geral_revistas = 0;
                $total_geral_ofertas = 0;
                $total_geral_assistencia = 0;

                foreach ($relatorios as $row):
                    $total_geral_matriculados += $row['total_matriculados'];
                    $total_geral_presencas += $row['total_presencas'];
                    $total_geral_faltas += $row['total_faltas'];
                    $total_geral_visitantes += $row['total_visitantes'];
                    $total_geral_biblias += $row['total_biblias'];
                    $total_geral_revistas += $row['total_revistas'];
                    $total_geral_ofertas += $row['total_ofertas'];
                    $total_geral_assistencia += $row['total_presencas'] + $row['total_visitantes'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['classe_nome']) ?></td>
                        <td><?= htmlspecialchars($row['congregacao_nome']) ?></td>
                        <td class="text-center"><?= $row['trimestre'] ?></td>
                        <td class="text-center"><?= $row['total_matriculados'] ?></td>
                        <td class="text-center"><?= $row['total_presencas'] ?></td>
                        <td class="text-center"><?= $row['total_faltas'] ?></td>
                        <td class="text-center"><?= $row['total_visitantes'] ?></td>
                        <td class="text-center"><?= $row['total_biblias'] ?></td>
                        <td class="text-center"><?= $row['total_revistas'] ?></td>
                        <td class="text-center"><?= number_format($row['total_ofertas'], 2, ',', '.') ?></td>
                        <td class="text-center"><?= $row['total_presencas'] + $row['total_visitantes'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-secondary fw-bold text-center">
                    <tr>
                        <td colspan="3" class="text-end">Totais Gerais:</td>
                        <td><?= $total_geral_matriculados ?></td>
                        <td><?= $total_geral_presencas ?></td>
                        <td><?= $total_geral_faltas ?></td>
                        <td><?= $total_geral_visitantes ?></td>
                        <td><?= $total_geral_biblias ?></td>
                        <td><?= $total_geral_revistas ?></td>
                        <td><?= number_format($total_geral_ofertas, 2, ',', '.') ?></td>
                        <td><?= $total_geral_assistencia ?></td>
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
    $(document).ready(function() {
        $('#tabela').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            },
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    className: 'btn btn-danger btn-sm',
                    text: '📄 PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-secondary btn-sm',
                    text: '🖨️ Imprimir'
                }
            ]
        });
    });
    </script>

</body>

</html>