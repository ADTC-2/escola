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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card-aluno {
            width: 100%;
            max-width: 350px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }

        .card-aluno:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-aluno .card-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #007bff;
        }

        .card-aluno .card-text {
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .card-aluno .btn {
            margin-right: 8px;
        }

        .btn-primary, .btn-warning, .btn-danger {
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover, .btn-warning:hover, .btn-danger:hover {
            background-color: #0056b3;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .card-container {
                justify-content: center;
                padding: 0 10px;
            }
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="#">Relatórios</a></li>
                    <li class="nav-item"><a class="nav-link" href="../congregacao/index.php">Congregações</a></li>
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
        <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
            <i class="fas fa-user-plus"></i>
        </button><br><br>

        <!-- Cards para visualização no celular -->
        <div class="card-container" id="cardsAlunos">
            <!-- Alunos serão carregados aqui -->
        </div>

        <!-- Paginação -->
        <nav id="pagination" aria-label="Page navigation example" class="mt-4">
            <ul class="pagination justify-content-center" id="paginationLinks">
                <!-- Links de paginação serão inseridos aqui -->
            </ul>
        </nav>
    </div>

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

    <!-- Modal Editar -->
    <div class="modal" id="modalEditarAluno" tabindex="-1" aria-labelledby="modalEditarAlunoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarAlunoLabel">Editar Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarAluno">
                        <div class="mb-3">
                            <label for="nomeEditar" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nomeEditar" required>
                        </div>
                        <div class="mb-3">
                            <label for="data_nascimentoEditar" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimentoEditar" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefoneEditar" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefoneEditar" oninput="mascaraTelefone(this)" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let paginaAtual = 1;
            const itensPorPagina = 6;

            // Função para listar alunos
            function listarAlunos(pagina = 1, filtro = '') {
                $.ajax({
                    url: "../../controllers/aluno.php",
                    type: "POST",
                    data: { acao: "listar", pagina: pagina, filtro: filtro },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (!data.sucesso) {
                            alert(data.mensagem);
                            return;
                        }

                        const alunos = data.data || [];
                        const totalAlunos = data.total || 0;
                        const cardsContainer = $("#cardsAlunos");
                        const paginationLinks = $("#paginationLinks");

                        cardsContainer.empty();
                        paginationLinks.empty();

                        alunos.forEach(aluno => {
                            const card = `
                                <div class="card-aluno">
                                    <h5 class="card-title">${aluno.nome}</h5>
                                    <p class="card-text">Data de Nascimento: ${formatarData(aluno.data_nascimento)}</p>
                                    <p class="card-text">Telefone: ${aluno.telefone}</p>
                                    <button class="btn btn-warning btn-sm btnEditar" data-id="${aluno.id}" data-nome="${aluno.nome}" data-data_nascimento="${aluno.data_nascimento}" data-telefone="${aluno.telefone}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btnExcluir" data-id="${aluno.id}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            `;
                            cardsContainer.append(card);
                        });

                        const totalPaginas = Math.ceil(totalAlunos / itensPorPagina);
                        for (let i = 1; i <= totalPaginas; i++) {
                            const link = `<li class="page-item ${i === pagina ? 'active' : ''}"><a class="page-link" href="#" data-pagina="${i}">${i}</a></li>`;
                            paginationLinks.append(link);
                        }
                    },
                    error: function(xhr, error, thrown) {
                        alert("Erro ao carregar dados: " + thrown);
                    }
                });
            }

            // Função para formatar a data
            function formatarData(data) {
                const date = new Date(data);
                const dia = ("0" + date.getDate()).slice(-2);
                const mes = ("0" + (date.getMonth() + 1)).slice(-2);
                const ano = date.getFullYear();
                return `${dia}/${mes}/${ano}`;
            }

            // Máscara de telefone
            function mascaraTelefone(input) {
                const valor = input.value.replace(/\D/g, '');
                if (valor.length <= 10) {
                    input.value = valor.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                } else {
                    input.value = valor.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                }
            }

            // Ação de busca
            $('#busca').on('keyup', function() {
                const filtro = $(this).val();
                listarAlunos(1, filtro);
            });

            // Navegar na paginação
            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const pagina = $(this).data('pagina');
                listarAlunos(pagina);
            });

            // Editar aluno
            $(document).on('click', '.btnEditar', function() {
                const aluno = $(this).data();
                $('#nomeEditar').val(aluno.nome);
                $('#data_nascimentoEditar').val(aluno.data_nascimento);
                $('#telefoneEditar').val(aluno.telefone);
                $('#modalEditarAluno').modal('show');
            });

            // Excluir aluno
            $(document).on('click', '.btnExcluir', function() {
                const id = $(this).data('id');
                if (confirm("Tem certeza que deseja excluir este aluno?")) {
                    $.ajax({
                        url: '../../controllers/aluno.php',
                        type: 'POST',
                        data: { acao: 'excluir', id: id },
                        success: function(response) {
                            const data = JSON.parse(response);
                            alert(data.mensagem);
                            if (data.sucesso) {
                                listarAlunos(paginaAtual);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert("Erro ao excluir aluno: " + error);
                        }
                    });
                }
            });

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
                        const data = JSON.parse(response);
                        alert(data.mensagem);
                        if (data.sucesso) {
                            listarAlunos(1);
                            $('#modalCadastrar').modal('hide');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Erro ao cadastrar aluno: " + error);
                    }
                });
            });

            // Inicializar a listagem de alunos
            listarAlunos(paginaAtual);
        });
    </script>
</body>

</html>









