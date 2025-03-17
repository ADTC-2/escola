<?php
// Incluir o arquivo de conexão PDO
require_once '../../config/conexao.php';
require_once '../includes/header.php';

// Consultar os dados da view
$query = "SELECT 
            congregacao_id, 
            nome_classe, 
            trimestre, 
            total_biblias, 
            total_revistas, 
            total_visitantes, 
            ofertas 
          FROM vw_congregacao_trimestre";
$stmt = $pdo->query($query);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Armazenar resultados em um array
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Congregação por Trimestre</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Estilos Personalizados -->
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
        .card-title {
            color: #0d6efd;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .card-text {
            color: #555;
            font-size: 1rem;
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
        @media (max-width: 768px) {
            .card-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Relatório de Congregação por Trimestre</h2>
        
        <!-- Tabela para Desktop -->
        <table id="congregacaoTable" class="table table-striped table-hover d-none d-md-table">
            <thead>
                <tr>
                    <th>Congregação ID</th>
                    <th>Classe</th>
                    <th>Trimestre</th>
                    <th>Bíblias</th>
                    <th>Revistas</th>
                    <th>Visitantes</th>
                    <th>Ofertas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['congregacao_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nome_classe']); ?></td>
                        <td><?php echo $row['trimestre']; ?></td>
                        <td><?php echo $row['total_biblias']; ?></td>
                        <td><?php echo $row['total_revistas']; ?></td>
                        <td><?php echo $row['total_visitantes']; ?></td>
                        <td><?php echo htmlspecialchars($row['ofertas']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <!-- Cards para Dispositivos Móveis -->
        <div class="card-container d-block d-md-none">
            <?php foreach ($results as $row) { ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['nome_classe']); ?></h5>
                        <p class="card-text"><strong>Congregação ID:</strong> <?php echo htmlspecialchars($row['congregacao_id']); ?></p>
                        <p class="card-text"><strong>Trimestre:</strong> <?php echo $row['trimestre']; ?></p>
                        <p class="card-text"><strong>Bíblias:</strong> <?php echo $row['total_biblias']; ?></p>
                        <p class="card-text"><strong>Revistas:</strong> <?php echo $row['total_revistas']; ?></p>
                        <p class="card-text"><strong>Visitantes:</strong> <?php echo $row['total_visitantes']; ?></p>
                        <p class="card-text"><strong>Ofertas:</strong> <?php echo htmlspecialchars($row['ofertas']); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS e Dependências -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Inicialização do DataTables -->
    <script>
        $(document).ready(function() {
            $('#congregacaoTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                responsive: true,
                order: [[0, 'asc']],
                paging: true,
                searching: true,
                autoWidth: false
            });
        });
    </script>
</body>
</html>

<?php
// Fechar a conexão com o banco
$pdo = null;
?>