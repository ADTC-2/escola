<?php
include('../../config/conexao.php');
include('../../views/includes/header.php');

// Recebe filtros
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$congregacao_id = $_GET['congregacao_id'] ?? '';
$trimestre = $_GET['trimestre'] ?? '';

// Ajusta datas com base no filtro trimestre
if (!empty($trimestre)) {
    $ano = date('Y');
    $mes_inicio = ($trimestre - 1) * 3 + 1;
    $mes_fim = $mes_inicio + 2;

    $data_inicio = "$ano-" . str_pad($mes_inicio, 2, '0', STR_PAD_LEFT) . "-01";
    $ultimo_dia = date("t", strtotime("$ano-" . str_pad($mes_fim, 2, '0', STR_PAD_LEFT) . "-01"));
    $data_fim = "$ano-" . str_pad($mes_fim, 2, '0', STR_PAD_LEFT) . "-$ultimo_dia";
} else {
    if (empty($data_inicio)) $data_inicio = date('Y-m-01');
    if (empty($data_fim)) $data_fim = date('Y-m-d');
}

// Consulta para total trimestral (sem mês)
$sql = "
SELECT 
    a.id AS aluno_id,
    a.nome AS aluno_nome,
    c.nome AS classe_nome,
    cg.nome AS congregacao_nome,
    CASE 
        WHEN MONTH(ch.data) BETWEEN 1 AND 3 THEN 1
        WHEN MONTH(ch.data) BETWEEN 4 AND 6 THEN 2
        WHEN MONTH(ch.data) BETWEEN 7 AND 9 THEN 3
        ELSE 4
    END AS trimestre,
    COUNT(DISTINCT ch.id) AS total_registros,
    COUNT(DISTINCT CASE WHEN p.presente = 'presente' THEN ch.id END) AS total_presencas,
    COUNT(DISTINCT CASE WHEN p.presente = 'ausente' THEN ch.id END) AS total_faltas
FROM alunos a
JOIN matriculas m ON m.aluno_id = a.id AND m.status = 'ativo'
JOIN classes c ON c.id = m.classe_id
JOIN congregacoes cg ON cg.id = m.congregacao_id
LEFT JOIN presencas p ON p.aluno_id = a.id
LEFT JOIN chamadas ch ON ch.id = p.chamada_id AND ch.classe_id = m.classe_id
WHERE ch.data BETWEEN :data_inicio AND :data_fim
  AND p.presente IN ('presente', 'ausente')
";

if (!empty($congregacao_id)) {
    $sql .= " AND cg.id = :congregacao_id";
}

$sql .= " GROUP BY a.id, trimestre ORDER BY a.nome, trimestre";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':data_inicio', $data_inicio);
$stmt->bindValue(':data_fim', $data_fim);
if (!empty($congregacao_id)) {
    $stmt->bindValue(':congregacao_id', $congregacao_id);
}
$stmt->execute();
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca congregações para filtro
$congs = $pdo->query("SELECT id, nome FROM congregacoes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Rankings top 10 presenças e faltas (baseado no total trimestral)
$top_presencas = $dados;
usort($top_presencas, fn($a, $b) => $b['total_presencas'] <=> $a['total_presencas']);
$top_presencas = array_slice($top_presencas, 0, 10);

$top_faltas = $dados;
usort($top_faltas, fn($a, $b) => $b['total_faltas'] <=> $a['total_faltas']);
$top_faltas = array_slice($top_faltas, 0, 10);

// Função para nome do trimestre
function nome_trimestre($num) {
    return "$numº Trimestre";
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório Trimestral de Presenças</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <style>
        .badge-presente { background-color: #28a745; }
        .badge-falta { background-color: #dc3545; }
        table.dataTable td { white-space: nowrap; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-4 px-3">
    <h4 class="mb-4 text-center">📊 Relatório Geral de Presenças por Trimestre</h4>

    <form class="row g-3 mb-4" method="GET">

        <div class="col-12 col-md-3">
            <label>Congregação:</label>
            <select name="congregacao_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach($congs as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($congregacao_id == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <label>Trimestre:</label>
            <select name="trimestre" class="form-select">
                <option value="">Todos</option>
                <option value="1" <?= ($trimestre == 1) ? 'selected' : '' ?>>1º Trimestre</option>
                <option value="2" <?= ($trimestre == 2) ? 'selected' : '' ?>>2º Trimestre</option>
                <option value="3" <?= ($trimestre == 3) ? 'selected' : '' ?>>3º Trimestre</option>
                <option value="4" <?= ($trimestre == 4) ? 'selected' : '' ?>>4º Trimestre</option>
            </select>
        </div>
        <div class="col-12 col-md-1 d-grid">
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
                        <td><?= nome_trimestre($d['trimestre']) ?></td>
                        <td><span class="badge badge-presente"><?= $d['total_presencas'] ?></span></td>
                        <td><span class="badge badge-falta"><?= $d['total_faltas'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

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

<!-- Scripts JS para DataTables -->
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
        buttons: [
            { extend: 'copyHtml5', text: 'Copiar', exportOptions: { columns: ':visible' } },
            { extend: 'excelHtml5', text: 'Excel', exportOptions: { columns: ':visible' } },
            { 
                extend: 'pdfHtml5', 
                text: 'PDF', 
                orientation: 'landscape', 
                pageSize: 'A4',
                title: 'Relatório Geral de Presenças por Trimestre',
                exportOptions: { columns: ':visible' },
                customize: function (doc) {
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 10,
                        color: 'white',
                        fillColor: '#343a40',
                        alignment: 'center'
                    };
                    doc.styles.title = {
                        fontSize: 14,
                        alignment: 'center',
                        bold: true
                    };
                    doc.defaultStyle.fontSize = 9;
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    doc.footer = function(currentPage, pageCount) {
                        return {
                            text: 'Página ' + currentPage.toString() + ' de ' + pageCount,
                            alignment: 'right',
                            margin: [0, 10, 20, 0],
                            fontSize: 8
                        };
                    };
                }
            },
            { extend: 'print', text: 'Imprimir', exportOptions: { columns: ':visible' } }
        ]
    });
});
</script>

</body>
</html>


