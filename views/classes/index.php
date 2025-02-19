<?php
require_once '../../auth/valida_sessao.php';
require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D - Classes</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">E.B.D - Painel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alunos/index.php">Alunos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../classes/index.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="../professores/index.php">Professores</a></li>
                    <li class="nav-item"><a class="nav-link" href="../congregacao/index.php">Congregações</a></li>
                    <li class="nav-item"><a class="nav-link" href="../matriculas/index.php">Matriculas</a></li>
                    <li class="nav-item"><a class="nav-link" href="../usuario/index.php">Usuários</a></li>
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
        <h2>Gerenciamento de Classes</h2>
        <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
        <i class="fas fa-plus-circle"></i> <span><strong>Adicionar Nova Classe</strong></span>
        </button><br><br>

        <table class="table table-striped" id="tabelaClasses">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Congregação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaClasses">
                <!-- Classes serão carregadas aqui -->
            </tbody>
        </table>
    </div>

    <!-- Modal Cadastrar -->
    <div class="modal" id="modalCadastrar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Classe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastrarClasse">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="congregacao_id" class="form-label">Congregação</label>
                            <select class="form-control" id="congregacao_id" required>
                                <!-- As opções serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal" id="modalEditarClasse" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Classe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarClasse">
                        <input type="hidden" id="idEditar">
                        <div class="mb-3">
                            <label for="nomeEditar" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nomeEditar" required>
                        </div>
                        <div class="mb-3">
                            <label for="congregacao_idEditar" class="form-label">Congregação</label>
                            <select class="form-control" id="congregacao_idEditar" required>
                                <!-- As opções serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Alterar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#tabelaClasses').DataTable({
            "serverSide": false,
            "ajax": {
                url: '../../controllers/classe.php',
                type: 'POST',
                data: { acao: 'listar' },
                dataType: "json",
                success: function(response) {
                    if (response.sucesso) {
                        table.clear().rows.add(response.data).draw();
                    } else {
                        console.error("Erro ao carregar dados:", response.mensagem);
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error("Erro no AJAX:", xhr.responseText);
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "nome" },
                { "data": "congregacao_nome" },
                {
                    "data": "id",
                    "render": function(data) {
                        return ` 
                            <button class='btn btn-warning btn-sm editar' data-id='${data}'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class='btn btn-danger btn-sm excluir' data-id='${data}'>
                                <i class="fas fa-trash-alt"></i>
                            </button>`;
                    }
                }
            ]
        });
        
        function carregarCongregacoes(selectedId = '') {
            $.post('../../controllers/congregacao.php', { acao: 'listar' }, function(response) {
                if (response.sucesso) {
                    let options = '<option value="">Selecione</option>';
                    response.data.forEach(c => {
                        options += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
                    });
                    $('#congregacao_id').html(options);
                    $('#congregacao_idEditar').html(options);
                } else {
                    console.error("Erro ao carregar congregações:", response.mensagem);
                }
            }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição das congregações:", textStatus, errorThrown);
            });
        }

        carregarCongregacoes();

        // Cadastro de Classe
        $("#formCadastrarClasse").submit(function(e) {
            e.preventDefault();
            $.post('../../controllers/classe.php', {
                acao: 'salvar',
                nome: $('#nome').val(),
                congregacao_id: $('#congregacao_id').val()
            }, function(response) {
                alert(response.mensagem);
                if (response.sucesso) {
                    $('#modalCadastrar').modal('hide');
                    table.ajax.reload();
                }
            }, 'json');
        });

        // Abrir modal de edição
        $('#tabelaClasses').on('click', '.editar', function() {
            let id = $(this).data('id');
            $.post('../../controllers/classe.php', { acao: 'buscar', id: id }, function(response) {
                if (response.sucesso) {
                    $('#idEditar').val(response.data.id);
                    $('#nomeEditar').val(response.data.nome);
                    carregarCongregacoes(response.data.congregacao_id);
                    $('#modalEditarClasse').modal('show');
                } else {
                    alert(response.mensagem);
                }
            }, 'json');
        });

        // Editar Classe
        $("#formEditarClasse").submit(function(e) {
            e.preventDefault();
            $.post('../../controllers/classe.php', {
                acao: 'editar',
                id: $('#idEditar').val(),
                nome: $('#nomeEditar').val(),
                congregacao_id: $('#congregacao_idEditar').val()
            }, function(response) {
                alert(response.mensagem);
                if (response.sucesso) {
                    $('#modalEditarClasse').modal('hide');
                    table.ajax.reload();
                }
            }, 'json');
        });

        // Exclusão de Classe
        $('#tabelaClasses').on('click', '.excluir', function() {
            let id = $(this).data('id');
            if (confirm("Tem certeza que deseja excluir esta classe?")) {
                $.post('../../controllers/classe.php', { acao: 'excluir', id: id }, function(response) {
                    alert(response.mensagem);
                    if (response.sucesso) {
                        table.ajax.reload();
                    }
                }, 'json');
            }
        });
    });
</script>

</body>

</html>

