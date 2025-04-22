<?php
// Conectando ao banco de dados
require_once '../../config/conexao.php';
require_once '../includes/header.php';

// Definir a localidade para português (Brasil)
setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');

// Consulta para pegar os aniversariantes do mês atual
$query = "SELECT nome, DAY(data_nascimento) AS dia 
          FROM alunos 
          WHERE MONTH(data_nascimento) = MONTH(CURRENT_DATE)";

$result = $pdo->query($query);

// Organizando os aniversariantes por dia
$aniversariantes = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $aniversariantes[$row['dia']][] = $row['nome'];
}

// Fechar a conexão com o banco
$pdo = null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Aniversariantes</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
        }
        h2 {
            color: #0d6efd;
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-align: center;
        }
        table.dataTable thead th {
            background-color: #0d6efd !important;
            color: #fff !important;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
        }
        .table td, .table th {
            vertical-align: middle;
            text-align: center;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Tabela de Aniversariantes -->
        <h2>Calendário de Aniversariantes - <?php echo strftime('%B %Y'); ?></h2>
        <table id="aniversariantes" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Nome</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($aniversariantes as $dia => $nomes) {
                    foreach ($nomes as $nome) {
                        echo "<tr><td>$dia</td><td>$nome</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS e Dependências -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <!-- Inicialização do DataTables -->
    <script>
        $(document).ready(function() {
            $('#aniversariantes').DataTable({
                dom: 'Bfrtip',
                buttons: ['pdf'],
                language: {
                    search: "Pesquisar:", // Personaliza o texto da busca
                    lengthMenu: "Exibir _MENU_ registros por página", // Personaliza a quantidade de registros por página
                    info: "Mostrando de _START_ até _END_ de _TOTAL_ registros", // Texto de informações
                    infoEmpty: "Nenhum registro encontrado", // Quando não houver resultados
                    zeroRecords: "Nenhum resultado encontrado", // Quando não encontrar resultados
                    paginate: {
                        first: "Primeira",
                        previous: "Anterior",
                        next: "Próxima",
                        last: "Última"
                    }
                }
            });
        });
    </script>
</body>
</html>




