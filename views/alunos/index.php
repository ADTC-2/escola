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
                        <input type="hidden" id="idEditar">  <!-- Campo oculto para o ID -->
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

    // Função para listar alunos com paginação
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


// Função para formatar a data corretamente no fuso do Brasil
function formatarData(dataString) {
    if (!dataString) {
        console.error("Data inválida");
        return null;
    }

    // Garantindo que a string está no formato correto (YYYY-MM-DD)
    const partes = dataString.split("-");
    if (partes.length !== 3) {
        console.error("Formato de data inválido:", dataString);
        return null;
    }

    // Criando uma data no formato correto (ano, mês - 1, dia) sem fuso horário
    const date = new Date(partes[0], partes[1] - 1, partes[2]);

    // Retorna no formato brasileiro DD/MM/AAAA
    return date.toLocaleDateString("pt-BR");
}






    // Editar aluno
    $(document).on('click', '.btnEditar', function() {
        const aluno = $(this).data();
        $('#idEditar').val(aluno.id);
        $('#nomeEditar').val(aluno.nome);
        $('#data_nascimentoEditar').val(aluno.data_nascimento);
        $('#telefoneEditar').val(aluno.telefone);
        $('#modalEditarAluno').modal('show');
    });

    // Atualizar dados do aluno (no modal)
    $("#formEditarAluno").on("submit", function(e) {
        e.preventDefault();
        
        const id = $("#idEditar").val();  // Pega o ID do aluno
        const nome = $("#nomeEditar").val();
        const data_nascimento = $("#data_nascimentoEditar").val();
        const telefone = $("#telefoneEditar").val();

        // Verificar se todos os campos estão preenchidos
        if (!nome || !data_nascimento || !telefone) {
            alert("Todos os campos são obrigatórios!");
            return;
        }

        // Envia os dados via AJAX para o controlador de edição
        $.ajax({
            url: '../../controllers/aluno.php',
            type: 'POST',
            data: {
                acao: 'editar',  // Ação para editar o aluno
                id: id,  // ID do aluno
                nome: nome,  // Nome do aluno
                data_nascimento: data_nascimento,  // Data de nascimento
                telefone: telefone  // Telefone do aluno
            },
            success: function(response) {
                const data = JSON.parse(response);
                alert(data.mensagem);  // Exibe a mensagem retornada pelo servidor

                if (data.sucesso) {
                    listarAlunos(paginaAtual);  // Atualiza a lista de alunos
                    $('#modalEditarAluno').modal('hide');  // Fecha o modal após o sucesso
                }
            },
            error: function(xhr, status, error) {
                alert("Erro ao editar aluno: " + error);
            }
        });
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

    // Cadastrar aluno
    $("#formCadastrarAluno").on("submit", function(e) {
        e.preventDefault();

        const nome = $("#nome").val();
        const data_nascimento = $("#data_nascimento").val();
        const telefone = $("#telefone").val();

        // Verificar se todos os campos estão preenchidos
        if (!nome || !data_nascimento || !telefone) {
            alert("Todos os campos são obrigatórios!");
            return;
        }

        // Envia os dados via AJAX para o controlador de cadastro
        $.ajax({
            url: '../../controllers/aluno.php',
            type: 'POST',
            data: {
                acao: 'salvar',  // Ação para cadastrar o aluno
                nome: nome,  // Nome do aluno
                data_nascimento: data_nascimento,  // Data de nascimento
                telefone: telefone  // Telefone do aluno
            },
            success: function(response) {
                const data = JSON.parse(response);
                alert(data.mensagem);  // Exibe a mensagem retornada pelo servidor

                if (data.sucesso) {
                    listarAlunos(paginaAtual);  // Atualiza a lista de alunos
                    $('#modalCadastrar').modal('hide');  // Fecha o modal após o sucesso
                }
            },
            error: function(xhr, status, error) {
                alert("Erro ao cadastrar aluno: " + error);
            }
        });
    });

    // Função de paginação
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const pagina = $(this).data('pagina');
        paginaAtual = pagina;
        listarAlunos(pagina);  // Atualiza a lista de alunos com a nova página
    });

    // Inicializar a listagem de alunos
    listarAlunos(paginaAtual);
});

</script>
</body>

</html>









