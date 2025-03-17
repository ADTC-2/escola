<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <!-- Menu lateral com ícones e um design mais moderno -->
        <div class="col-md-3">
            <div class="list-group shadow-sm rounded">
                <a href="./total_alunos.php" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-users"></i> Frequencia de Alunos
                </a>
                <a href="./total_rev_bibl_vist.php" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-user-times"></i> Revistas e Bíblias
                </a>
                <a href="#totalPresentes" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-user-check"></i> Total de Presentes
                </a>
                <a href="#totalVisitantes" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-users-slash"></i> Total de Visitantes
                </a>
                <a href="#totalOfertas" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-hand-holding-usd"></i> Total de Ofertas
                </a>
                <a href="#revistasBibles" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-book-open"></i> Revistas e Bíblias
                </a>
                <a href="#frequencia" class="list-group-item list-group-item-action p-3" data-toggle="collapse">
                    <i class="fas fa-calendar-check"></i> Frequência
                </a>
            </div>
        </div>

        <!-- Conteúdo principal -->
        <div class="col-md-9">
            <!-- Seção Total de Alunos -->
            <div class="collapse" id="totalAlunos">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Total de Alunos</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir o número total de alunos.</p>
                        <!-- Exemplo de gráfico ou tabela -->
                    </div>
                </div>
            </div>

            <!-- Seção Total de Ausentes -->
            <div class="collapse" id="totalAusentes">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5>Total de Ausentes</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir o número total de alunos ausentes.</p>
                    </div>
                </div>
            </div>

            <!-- Seção Total de Presentes -->
            <div class="collapse" id="totalPresentes">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5>Total de Presentes</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir o número total de alunos presentes.</p>
                    </div>
                </div>
            </div>

            <!-- Seção Total de Visitantes -->
            <div class="collapse" id="totalVisitantes">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5>Total de Visitantes</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir o número total de visitantes.</p>
                    </div>
                </div>
            </div>

            <!-- Seção Total de Ofertas -->
            <div class="collapse" id="totalOfertas">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5>Total de Ofertas</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir o total de ofertas arrecadadas.</p>
                    </div>
                </div>
            </div>

            <!-- Seção Revistas e Bíblias -->
            <div class="collapse" id="revistasBibles">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5>Revistas e Bíblias</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir o total de revistas e bíblias distribuídas.</p>
                    </div>
                </div>
            </div>

            <!-- Seção Frequência -->
            <div class="collapse" id="frequencia">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Frequência</h5>
                    </div>
                    <div class="card-body">
                        <p>Aqui você pode exibir a frequência de cada aluno.</p>
                        <!-- Exemplo de tabela -->
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>João Silva</td>
                                    <td><span class="badge bg-success">Presente</span></td>
                                </tr>
                                <tr>
                                    <td>Ana Pereira</td>
                                    <td><span class="badge bg-danger">Ausente</span></td>
                                </tr>
                                <tr>
                                    <td>Pedro Souza</td>
                                    <td><span class="badge bg-success">Presente</span></td>
                                </tr>
                                <tr>
                                    <td>Maria Oliveira</td>
                                    <td><span class="badge bg-danger">Ausente</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</body>
</html>


