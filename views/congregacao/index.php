<?php  
    require_once '../../auth/valida_sessao.php';
    require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D - Congregações</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
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
        <h2>Gerenciamento de Congregações</h2>
        <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
            <i class="fas fa-plus"></i> Adicionar Congregação
        </button><br><br>

        <!-- Tabela de Congregações com DataTable -->
        <table class="table table-striped" id="tabelaCongregacoes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaCongregacoes">
                <!-- Congregações serão carregadas aqui -->
            </tbody>
        </table>
    </div>

    <!-- Modal Cadastrar -->
    <div class="modal" id="modalCadastrar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Congregação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastrarCongregacao">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal" id="modalEditarCongregacao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Congregação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCongregacao">
                        <input type="hidden" id="idEditar">
                        <div class="mb-3">
                            <label for="nomeEditar" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nomeEditar" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Adicionando o script do DataTables -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    function carregarCongregacoes() {
        $.ajax({
            url: "../../controllers/congregacao.php",
            type: "POST",
            data: { acao: "listar" },
            success: function(response) {
                const tbody = $("#listaCongregacoes");
                tbody.empty();
                
                if (response.sucesso && response.data.length > 0) {
                    response.data.forEach(cong => {
                        tbody.append(`
                            <tr>
                                <td>${cong.id}</td>
                                <td>${cong.nome}</td>
                                <td>
                                    <button class="btn btn-warning btnEditar" data-id="${cong.id}" data-nome="${cong.nome}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btnExcluir" data-id="${cong.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">Nenhuma congregação encontrada</td></tr>');
                }

                // Destruir qualquer instância do DataTable antes de inicializar uma nova
                if ($.fn.DataTable.isDataTable('#tabelaCongregacoes')) {
                    $('#tabelaCongregacoes').DataTable().clear().destroy();
                }

                // Inicializando o DataTable
                $("#tabelaCongregacoes").DataTable({
                    "language": {
                        "url": "/caminho/para/o/arquivo/Portuguese.json"  // Altere para o caminho correto do arquivo JSON local
                    }
                });
            },
            error: function() {
                alert("Erro ao carregar as congregações.");
            }
        });
    }

    $(document).ready(function() {
        carregarCongregacoes();

        // Abrir o modal de edição e preencher os campos
        $(document).on("click", ".btnEditar", function() {
            const id = $(this).data("id");
            const nome = $(this).data("nome");

            // Preencher os campos do modal de edição
            $("#idEditar").val(id);
            $("#nomeEditar").val(nome);

            // Abrir o modal de edição
            $("#modalEditarCongregacao").modal("show");
        });

        // Salvar nova congregação
        $("#formCadastrarCongregacao").submit(function(e) {
            e.preventDefault();
            const nome = $("#nome").val().trim();

            if (nome === "") {
                alert("O nome da congregação não pode estar vazio.");
                return;
            }

            $.ajax({
                url: "../../controllers/congregacao.php",
                type: "POST",
                data: { acao: "salvar", nome: nome },
                success: function(response) {
                    alert(response.mensagem);
                    if (response.sucesso) {
                        $("#modalCadastrar").modal("hide");
                        carregarCongregacoes();
                    }
                },
                error: function() {
                    alert("Erro ao salvar a congregação.");
                }
            });
        });

        // Editar congregação
        $("#formEditarCongregacao").submit(function(e) {
            e.preventDefault();
            const id = $("#idEditar").val();
            const nome = $("#nomeEditar").val().trim();

            if (nome === "") {
                alert("O nome da congregação não pode estar vazio.");
                return;
            }

            $.ajax({
                url: "../../controllers/congregacao.php",
                type: "POST",
                data: { acao: "editar", id: id, nome: nome },
                success: function(response) {
                    alert(response.mensagem);
                    if (response.sucesso) {
                        $("#modalEditarCongregacao").modal("hide");
                        carregarCongregacoes();
                    }
                },
                error: function() {
                    alert("Erro ao editar a congregação.");
                }
            });
        });

        // Excluir congregação
        $(document).on("click", ".btnExcluir", function() {
            if (confirm("Deseja excluir esta congregação?")) {
                $.ajax({
                    url: "../../controllers/congregacao.php",
                    type: "POST",
                    data: { acao: "excluir", id: $(this).data("id") },
                    success: function(response) {
                        alert(response.mensagem);
                        if (response.sucesso) {
                            carregarCongregacoes();
                        }
                    },
                    error: function() {
                        alert("Erro ao excluir a congregação.");
                    }
                });
            }
        });
    });
</script>



</body>

</html>

