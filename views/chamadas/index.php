<?php  
    require_once '../../auth/valida_sessao.php';
    require_once '../../config/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamadas</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Chamadas</h1>
        <table id="tabelaChamadas" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Classe</th>
                    <th>Professor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("
                    SELECT c.id, c.data, cl.nome AS classe, p.usuario_id AS professor
                    FROM chamadas c
                    JOIN classes cl ON c.classe_id = cl.id
                    JOIN professores p ON c.professor_id = p.id
                ");
                while ($row = $stmt->fetch()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['data']}</td>
                            <td>{$row['classe']}</td>
                            <td>{$row['professor']}</td>
                            <td>
                                <a href='presenca.php?chamada_id={$row['id']}' class='btn btn-primary btn-sm'>Registrar Presença</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="nova_chamada.php" class="btn btn-primary">Nova Chamada</a>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelaChamadas').DataTable();
        });
    </script>
</body>
</html>