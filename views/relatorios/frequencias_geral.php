<?php
require_once('../../config/conexao.php');
include('../../views/includes/header.php');

// --- Filtros ---
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim    = $_GET['data_fim'] ?? '';
$congregacao_id = $_GET['congregacao_id'] ?? '';
$classe_id = $_GET['classe_id'] ?? '';
$trimestre = $_GET['trimestre'] ?? '';
$limpar_duplicatas = isset($_GET['limpar_duplicatas']) ? true : false;

// --- Função para pegar nome classe ---
function getNomeClasse($pdo, $classe_id) {
    $stmt = $pdo->prepare("SELECT nome FROM classes WHERE id = :id");
    $stmt->execute([':id' => $classe_id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return $r ? $r['nome'] : "Classe ID $classe_id";
}

// --- Limpeza de chamadas duplicadas se pedido ---
if ($limpar_duplicatas) {
    $where = [];
    $params = [];

    if ($classe_id !== '') {
        $where[] = 'c1.classe_id = :classe_id';
        $params[':classe_id'] = $classe_id;
    }
    if ($data_inicio && $data_fim) {
        $where[] = 'c1.data BETWEEN :data_inicio AND :data_fim';
        $params[':data_inicio'] = $data_inicio;
        $params[':data_fim'] = $data_fim;
    }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sqlDelete = "DELETE c1 FROM chamadas c1
                  INNER JOIN chamadas c2
                    ON c1.data = c2.data
                    AND c1.classe_id = c2.classe_id
                    AND c1.id > c2.id
                  $whereSql";

    $stmtDel = $pdo->prepare($sqlDelete);
    foreach ($params as $k => $v) {
        $stmtDel->bindValue($k, $v);
    }
    $stmtDel->execute();

    $msg_limpeza = "Limpeza de chamadas duplicadas realizada com sucesso.";
}

// --- Consulta chamadas duplicadas ---
$whereDup = [];
$paramsDup = [];
if ($classe_id !== '') {
    $whereDup[] = 'classe_id = :classe_id_dup';
    $paramsDup[':classe_id_dup'] = $classe_id;
}
if ($data_inicio && $data_fim) {
    $whereDup[] = 'data BETWEEN :data_inicio_dup AND :data_fim_dup';
    $paramsDup[':data_inicio_dup'] = $data_inicio;
    $paramsDup[':data_fim_dup'] = $data_fim;
}
$whereSqlDup = $whereDup ? 'WHERE ' . implode(' AND ', $whereDup) : '';

$sqlDup = "SELECT data, classe_id, COUNT(*) AS total_chamadas
           FROM chamadas
           $whereSqlDup
           GROUP BY data, classe_id
           HAVING total_chamadas > 1
           ORDER BY data";

$stmtDup = $pdo->prepare($sqlDup);
foreach ($paramsDup as $k => $v) {
    $stmtDup->bindValue($k, $v);
}
$stmtDup->execute();
$duplicatas = $stmtDup->fetchAll(PDO::FETCH_ASSOC);

// --- Ajuste intervalo datas por trimestre ---
if ($trimestre && (!$data_inicio || !$data_fim)) {
    $ano = date('Y');
    switch ($trimestre) {
        case '1': $data_inicio = "$ano-01-01"; $data_fim = "$ano-03-31"; break;
        case '2': $data_inicio = "$ano-04-01"; $data_fim = "$ano-06-30"; break;
        case '3': $data_inicio = "$ano-07-01"; $data_fim = "$ano-09-30"; break;
        case '4': $data_inicio = "$ano-10-01"; $data_fim = "$ano-12-31"; break;
    }
}

// --- Consulta principal ---
$sqlRel = "
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
    DATE_FORMAT(ch.data, '%Y-%m') AS mes,
    COUNT(DISTINCT ch.id) AS total_registros,
    COUNT(DISTINCT CASE WHEN p.presente IN ('presente', 'justificado') THEN ch.id END) AS total_presencas,
    COUNT(DISTINCT CASE WHEN p.presente = 'ausente' THEN ch.id END) AS total_faltas
FROM alunos a
JOIN matriculas m ON m.aluno_id = a.id AND m.status = 'ativo'
JOIN classes c ON c.id = m.classe_id
JOIN congregacoes cg ON cg.id = m.congregacao_id
LEFT JOIN presencas p ON p.aluno_id = a.id
LEFT JOIN chamadas ch ON ch.id = p.chamada_id AND ch.classe_id = m.classe_id
WHERE 1=1
";

if ($data_inicio && $data_fim) {
    $sqlRel .= " AND ch.data BETWEEN :data_inicio AND :data_fim ";
}
if ($congregacao_id) {
    $sqlRel .= " AND m.congregacao_id = :congregacao_id ";
}
if ($classe_id) {
    $sqlRel .= " AND m.classe_id = :classe_id ";
}
if ($trimestre) {
    $sqlRel .= " AND 
        CASE 
            WHEN MONTH(ch.data) BETWEEN 1 AND 3 THEN 1
            WHEN MONTH(ch.data) BETWEEN 4 AND 6 THEN 2
            WHEN MONTH(ch.data) BETWEEN 7 AND 9 THEN 3
            ELSE 4
        END = :trimestre ";
}

$sqlRel .= " GROUP BY a.id, trimestre, mes ORDER BY a.nome, trimestre, mes ";

$stmtRel = $pdo->prepare($sqlRel);

if ($data_inicio && $data_fim) {
    $stmtRel->bindValue(':data_inicio', $data_inicio);
    $stmtRel->bindValue(':data_fim', $data_fim);
}
if ($congregacao_id) {
    $stmtRel->bindValue(':congregacao_id', $congregacao_id);
}
if ($classe_id) {
    $stmtRel->bindValue(':classe_id', $classe_id);
}
if ($trimestre) {
    $stmtRel->bindValue(':trimestre', $trimestre);
}

$stmtRel->execute();
$relatorios = $stmtRel->fetchAll(PDO::FETCH_ASSOC);

// --- Carregar dropdowns ---
$congs = $pdo->query("SELECT id, nome FROM congregacoes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$classes = $pdo->query("SELECT id, nome FROM classes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Relatório de Presenças e Limpeza de Chamadas Duplicadas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />

<style>
  .badge-presente { background-color: #28a745; color: white; }
  .badge-falta { background-color: #dc3545; color: white; }
  body { background-color: #f8f9fa; }
</style>
</head>
<body>

<div class="container my-4">
  <h3 class="text-center mb-4">📊 Relatório de Presenças por Aluno</h3>

  <!-- Filtros -->
  <form method="get" class="row g-3 mb-4">
    <div class="col-md-2">
      <label for="data_inicio" class="form-label">Data Início</label>
      <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= htmlspecialchars($data_inicio) ?>">
    </div>
    <div class="col-md-2">
      <label for="data_fim" class="form-label">Data Fim</label>
      <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= htmlspecialchars($data_fim) ?>">
    </div>
    <div class="col-md-2">
      <label for="congregacao_id" class="form-label">Congregação</label>
      <select name="congregacao_id" id="congregacao_id" class="form-select">
        <option value="">Todas</option>
        <?php foreach($congs as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($congregacao_id == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label for="classe_id" class="form-label">Classe</label>
      <select name="classe_id" id="classe_id" class="form-select">
        <option value="">Todas</option>
        <?php foreach($classes as $cl): ?>
          <option value="<?= $cl['id'] ?>" <?= ($classe_id == $cl['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cl['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label for="trimestre" class="form-label">Trimestre</label>
      <select name="trimestre" id="trimestre" class="form-select">
        <option value="">Todos</option>
        <?php for ($i=1; $i<=4; $i++): ?>
          <option value="<?= $i ?>" <?= ($trimestre == $i) ? 'selected' : '' ?>><?= $i ?>º</option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-2 d-flex align-items-end gap-2">
      <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
      <button type="submit" name="limpar_duplicatas" value="1" class="btn btn-danger" onclick="return confirm('Confirma limpeza de chamadas duplicadas?')">Limpar Chamadas Duplicadas</button>
    </div>
  </form>

<?php if (!empty($msg_limpeza)): ?>
  <div class="alert alert-success" id="msg-limpeza"><?= htmlspecialchars($msg_limpeza) ?></div>
<?php endif; ?>


  <!-- Relatório -->
  <div class="table-responsive">
    <table id="tabela" class="table table-bordered table-striped nowrap w-100">
      <thead class="table-dark">
        <tr>
          <th>Aluno</th>
          <th>Classe</th>
          <th>Congregação</th>
          <th>Trimestre</th>
          <th>Mês/Ano</th>
          <th>Presenças</th>
          <th>Faltas</th>
          <th>Total Registros</th>
          <th>%</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sumP = $sumF = $sumT = 0;
        $currentAluno = null;
        $alunoP = $alunoF = $alunoT = 0;
        
        foreach ($relatorios as $index => $r):
          $p = (int)$r['total_presencas'];
          $f = (int)$r['total_faltas'];
          $t = (int)$r['total_registros'];
          $perc = $t > 0 ? round($p / $t * 100, 2) : 0;

          // Se mudou de aluno, adiciona linha de total
          if ($currentAluno !== null && $currentAluno != $r['aluno_id']) {
            $alunoPerc = $alunoT > 0 ? round($alunoP / $alunoT * 100, 2) : 0;
            ?>
            <tr class="total-row">
              <td colspan="5" class="text-end">Total do Aluno:</td>
              <td class="text-center"><?= $alunoP ?></td>
              <td class="text-center"><?= $alunoF ?></td>
              <td class="text-center"><?= $alunoT ?></td>
              <td class="text-center"><?= $alunoPerc ?>%</td>
            </tr>
            <?php
            $alunoP = $alunoF = $alunoT = 0;
          }
          
          $currentAluno = $r['aluno_id'];
          $alunoP += $p;
          $alunoF += $f;
          $alunoT += $t;
          
          $sumP += $p;
          $sumF += $f;
          $sumT += $t;
        ?>
        <tr>
          <td><?= htmlspecialchars($r['aluno_nome']) ?></td>
          <td><?= htmlspecialchars($r['classe_nome']) ?></td>
          <td><?= htmlspecialchars($r['congregacao_nome']) ?></td>
          <td class="text-center"><?= $r['trimestre'] ?>º</td>
          <td class="text-center"><?= date('m/Y', strtotime($r['mes'].'-01')) ?></td>
          <td class="text-center"><span class="badge badge-presente"><?= $p ?></span></td>
          <td class="text-center"><span class="badge badge-falta"><?= $f ?></span></td>
          <td class="text-center"><?= $t ?></td>
          <td class="text-center"><?= $perc ?>%</td>
        </tr>
        <?php 
          // Adiciona total do último aluno
          if ($index === count($relatorios) - 1) {
            $alunoPerc = $alunoT > 0 ? round($alunoP / $alunoT * 100, 2) : 0;
            ?>
            <tr class="total-row">
            <td colspan="5" class="text-end">Total do Aluno:</td>
            <td class="text-center"><?= $alunoP ?></td>
            <td class="text-center"><?= $alunoF ?></td>
            <td class="text-center"><?= $alunoT ?></td>
            <td class="text-center"><?= $alunoPerc ?>%</td>
            </tr>
            <?php
          }
        endforeach; 
        ?>
      </tbody>
      <tfoot class="table-secondary fw-bold">
        <tr class="total-geral-row">
            <td colspan="5" class="text-end">Totais Gerais:</td>
            <td class="text-center"><?= $sumP ?></td>
            <td class="text-center"><?= $sumF ?></td>
            <td class="text-center"><?= $sumT ?></td>
            <td class="text-center"><?= $sumT > 0 ? round($sumP / $sumT * 100, 2).'%' : '0%' ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Chamadas Duplicadas Encontradas -->
  <h4 class="mt-5">Chamadas Duplicadas Encontradas</h4>
  <?php if ($duplicatas): ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Data</th>
            <th>Classe</th>
            <th>Total Chamadas</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($duplicatas as $d): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($d['data'])) ?></td>
              <td><?= htmlspecialchars(getNomeClasse($pdo, $d['classe_id'])) ?></td>
              <td><?= $d['total_chamadas'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">Nenhuma chamada duplicada encontrada no filtro selecionado.</div>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
$(function(){
  $('#tabela').DataTable({
    responsive: true,
    scrollX: true,
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: '📊 Excel', exportOptions: {columns: ':visible'} },
      { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: '📄 PDF', orientation: 'landscape', pageSize: 'A4', exportOptions: {columns: ':visible'} },
      { extend: 'print', className: 'btn btn-secondary btn-sm', text: '🖨️ Imprimir', exportOptions: {columns: ':visible'} }
    ],
    order: [[0, 'asc'], [3, 'asc'], [4, 'asc']],
    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" }
  });
});
</script>
<script>
    setTimeout(function() {
    const alert = document.getElementById('msg-limpeza');
    if (alert) {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 500);
    }
    }, 5000); // 5000 milissegundos = 5 segundos
</script>


</body>
</html>

