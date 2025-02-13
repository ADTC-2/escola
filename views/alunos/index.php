<?php  
    require_once '../../auth/valida_sessao.php';
    require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> 
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        @media (max-width: 768px) {
            .table-responsive {
                display: block;
            }
            .card-container {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .card-aluno {
                border: 1px solid #ddd;
                padding: 15px;
                border-radius: 8px;
                width: 100%;
                background-color: #f8f9fa;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">E.B.D - Painel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alunos/index.php">Alunos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../professores/index.php">Professores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Relatórios</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Cadastro de Alunos</h2>

        <!-- Tabela de Alunos -->
        <table id="tabelaAlunos" class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Nascimento</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="lista-alunos">
                <!-- Alunos serão carregados aqui -->
            </tbody>
        </table>

        <!-- Modal Cadastrar -->
        <div class="modal" id="modalCadastrar" tabindex="-1" aria-labelledby="modalCadastrarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCadastrarLabel">Cadastrar Aluno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCadastrarAluno">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="data_nascimento" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone</label>                                 
                                <input type="text" class="form-control" id="telefone" oninput="mascaraTelefone(this)" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
            Cadastrar Aluno
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
        //DataTable
        $(document).ready(function() {
            var table = $('#tabelaAlunos').DataTable({
                "ajax": {
                    "url": "../../controllers/aluno.php",
                    "type": "POST",
                    "data": { acao: "listar" },  // Corrigido para "listar"
                    "dataSrc": function(json) {
                        if (!json.sucesso) {
                            exibirMensagem(json.mensagem, "danger");
                            return [];
                        }
                        // Certifique-se de que a resposta contém um array "data"
                        var alunos = json.data || [];
                        var output = [];
                        alunos.forEach(function(aluno) {
                            output.push([
                                aluno.nome,
                                aluno.data_nascimento,
                                aluno.telefone,
                                '<button class="btn btn-danger btnExcluir" data-id="'+ aluno.id +'">Excluir</button>'
                            ]);
                        });
                        return output;
                    },
                    "error": function(xhr, error, thrown) {
                        exibirMensagem("Erro ao carregar dados: " + thrown, "danger");
                    }
                }
            });

            // Função para exibir alertas Bootstrap 5
            function exibirMensagem(mensagem, tipo) {
                $("#alerta").html(`
                    <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                        ${mensagem}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
            }

            // Cadastro de aluno
            $("#formCadastrarAluno").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    url: '../../controllers/aluno.php',
                    type: 'POST',
                    data: {
                        acao: 'salvar',
                        nome: $("#nome").val(),
                        data_nascimento: $("#data_nascimento").val(),
                        telefone: $("#telefone").val()
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        exibirMensagem(data.mensagem, data.sucesso ? "success" : "danger");
                        if (data.sucesso) {
                            table.ajax.reload();
                            $("#formCadastrarAluno")[0].reset();
                        }
                    },
                    error: function(xhr, status, error) {
                        exibirMensagem("Erro ao cadastrar: " + error, "danger");
                    }
                });
            });

            // Excluir aluno
            $('#tabelaAlunos').on('click', '.btnExcluir', function() {
                var id = $(this).data('id');

                if (confirm("Tem certeza que deseja excluir este aluno?")) {
                    $.ajax({
                        url: '../../controllers/aluno.php',
                        type: 'POST',
                        data: { acao: 'excluir', id: id },
                        success: function(response) {
                            var data = JSON.parse(response);
                            exibirMensagem(data.mensagem, data.sucesso ? "success" : "danger");
                            if (data.sucesso) {
                                table.ajax.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            exibirMensagem("Erro ao excluir aluno: " + error, "danger");
                        }
                    });
                }
            });
        });

        //Função para mascara de telefone

        function mascaraTelefone(input) {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length > 11) {
                valor = valor.substring(0, 11);
            }
            if (valor.length <= 10) {
                input.value = valor.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            } else {
                input.value = valor.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            }
        }
</script>
</body>
</html>
