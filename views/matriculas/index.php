<?php
    require_once '../../auth/valida_sessao.php';
    require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D - Matrículas</title>
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
                    <li class="nav-item"><a class="nav-link" href="../matriculas/index.php">Matrículas</a></li>
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
        <h2>Gerenciamento de Matrículas</h2>
        <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
            <i class="fas fa-plus"></i> Nova Matrícula
        </button><br><br>

        <!-- Tabela de Matrículas com DataTable -->
        <table class="table table-striped" id="tabelaMatriculas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aluno</th>
                    <th>Classe</th>
                    <th>Data Matrícula</th>
                    <th>Status</th>
                    <th>Trimestre</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaMatriculas">
                <!-- Matrículas serão carregadas aqui -->
            </tbody>
        </table>
    </div>

    <!-- Modal Cadastrar Matrícula -->
    <div class="modal" id="modalCadastrar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Matrícula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastrarMatricula">
                        <div class="mb-3">
                            <label for="aluno" class="form-label">Aluno</label>
                            <select class="form-control" id="aluno" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="classe" class="form-label">Classe</label>
                            <select class="form-control" id="classe" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="trimestre" class="form-label">Trimestre</label>
                            <select class="form-control" id="trimestre" required>
                                <option value="1">1º Trimestre</option>
                                <option value="2">2º Trimestre</option>
                                <option value="3">3º Trimestre</option>
                                <option value="4">4º Trimestre</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar Matrícula</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Matrícula -->
    <div class="modal" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Matrícula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMatricula">
                        <input type="hidden" id="matricula_id">
                        <div class="mb-3">
                            <label for="aluno_editar" class="form-label">Aluno</label>
                            <select class="form-control" id="aluno_editar" required>
                                <!-- Preencher com alunos via Ajax -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="classe_editar" class="form-label">Classe</label>
                            <select class="form-control" id="classe_editar" required>
                                <!-- Preencher com classes via Ajax -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="trimestre_editar" class="form-label">Trimestre</label>
                            <select class="form-control" id="trimestre_editar" required>
                                <option value="1">1º Trimestre</option>
                                <option value="2">2º Trimestre</option>
                                <option value="3">3º Trimestre</option>
                                <option value="4">4º Trimestre</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Excluir Matrícula -->
    <div class="modal" id="modalExcluir" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Excluir Matrícula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir esta matrícula?</p>
                    <button type="button" class="btn btn-danger" id="confirmarExcluir">Excluir</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    // Carregar alunos e classes ao iniciar
    carregarAlunos();
    carregarClasses();
    carregarMatriculas();
    let selectedId = null;

        $.post('../../controllers/matriculas.php', { acao: 'listarAlunos' }, function(response) {
        if (response.sucesso) {
            let options = '<option value="">Selecione</option>';
            response.data.forEach(a => {
                options += `<option value="${a.id}" ${a.id == selectedId ? 'selected' : ''}>${a.nome}</option>`;
            });
            $('#aluno').html(options);
            $('#aluno_editar').html(options);
        } else {
            console.error("Erro ao carregar alunos:", response.mensagem);
        }
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Erro na requisição dos alunos:", textStatus, errorThrown);
        console.error("Resposta do servidor:", jqXHR.responseText); // Exibe a resposta completa
    });


    function carregarAlunos(selectedId = '') {
        $.post('../../controllers/matriculas.php', { acao: 'listarAlunos' }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(a => {
                    options += `<option value="${a.id}" ${a.id == selectedId ? 'selected' : ''}>${a.nome}</option>`;
                });
                $('#aluno').html(options);
                $('#aluno_editar').html(options);
            } else {
                console.error("Erro ao carregar alunos:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição dos alunos:", textStatus, errorThrown);
            console.error("Resposta do servidor:", jqXHR.responseText); // Exibe a resposta completa
        });
    }

    function carregarClasses(selectedId = '') {
        $.post('../../controllers/matriculas.php', { acao: 'listarClasses' }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(c => {
                    options += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
                });
                $('#classe').html(options);
                $('#classe_editar').html(options);
            } else {
                console.error("Erro ao carregar classes:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição das classes:", textStatus, errorThrown);
        });
    }

    // Salvar nova matrícula
    $("#formCadastrarMatricula").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: { 
                acao: "matricular", 
                aluno_id: $("#aluno").val(),
                classe_id: $("#classe").val(),
                trimestre: $("#trimestre").val()
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    alert("Matrícula realizada com sucesso!");
                    $("#modalCadastrar").modal("hide");
                    carregarMatriculas();
                } else {
                    alert("Erro ao realizar matrícula.");
                }
            },
            error: function() {
                alert("Erro ao tentar matricular.");
            }
        });
    });

    // Excluir matrícula
    $("#confirmarExcluir").click(function() {
        let matricula_id = $("#matricula_id").val();
        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: { 
                acao: "excluirMatricula", 
                matricula_id: matricula_id
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    alert("Matrícula excluída com sucesso!");
                    $("#modalExcluir").modal("hide");
                    carregarMatriculas();
                } else {
                    alert("Erro ao excluir matrícula.");
                }
            },
            error: function() {
                alert("Erro ao tentar excluir matrícula.");
            }
        });
    });
});

function carregarMatriculas() {
    $.ajax({
        url: "../../controllers/matriculas.php",
        type: "POST",
        data: { acao: "listarMatriculas" },
        dataType: "json",
        success: function(response) {
            console.log(response); // Depuração da resposta
            if (response.sucesso) {
                let rows = '';
                response.matriculas.forEach(matricula => {
                    rows += `
                        <tr>
                            <td>${matricula.id}</td>
                            <td>${matricula.aluno_nome}</td>
                            <td>${matricula.classe_nome}</td>
                            <td>${matricula.data_matricula}</td>
                            <td>${matricula.status}</td>
                            <td>${matricula.trimestre}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editarMatricula(${matricula.id})">Editar</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmarExcluir(${matricula.id})">Excluir</button>
                            </td>
                        </tr>
                    `;
                });
                $("#listaMatriculas").html(rows);
            } else {
                alert('Erro ao carregar matrículas.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown); // Exibe erro no console
            alert("Erro ao carregar matrículas.");
        }
    });
}

// Função para carregar os dados da matrícula no modal
function editarMatricula(id) {
    // Enviar a requisição para carregar os dados da matrícula
    $.ajax({
        url: "../../controllers/matriculas.php",
        type: "POST",
        data: { 
            acao: "carregarMatricula", 
            matricula_id: id
        },
        dataType: "json",
        success: function(response) {
            if (response.sucesso) {
                let matricula = response.matricula;
                
                // Preencher os campos do modal com os dados da matrícula
                $("#matricula_id").val(matricula.id);
                $("#aluno_editar").val(matricula.aluno_id); // Aluno
                $("#classe_editar").val(matricula.classe_id); // Classe
                $("#trimestre_editar").val(matricula.trimestre); // Trimestre

                // Exibir o modal de edição
                $("#modalEditar").modal("show");
            } else {
                alert("Erro ao carregar dados para edição.");
            }
        },
        error: function() {
            alert("Erro ao tentar carregar matrícula para edição.");
        }
    });
}

// Enviar dados para editar a matrícula
$("#formEditarMatricula").submit(function(e) {
    e.preventDefault();

    let aluno_id = $("#aluno_editar").val();
    let classe_id = $("#classe_editar").val();
    let trimestre = $("#trimestre_editar").val();
    let matricula_id = $("#matricula_id").val();

    // Verifique se todos os campos obrigatórios estão preenchidos
    if (!aluno_id || !classe_id || !trimestre || !matricula_id) {
        alert("Todos os campos devem ser preenchidos!");
        return;
    }

    // Enviar os dados para o backend para editar a matrícula
    $.ajax({
        url: "../../controllers/matriculas.php",
        type: "POST",
        data: { 
            acao: "editarMatricula", 
            matricula_id: matricula_id,
            aluno_id: aluno_id,  // Passando o aluno_id
            classe_id: classe_id,  // Passando o classe_id
            trimestre: trimestre  // Passando o trimestre
        },
        dataType: "json",
        success: function(response) {
            if (response.sucesso) {
                alert("Matrícula editada com sucesso!");
                $("#modalEditar").modal("hide");
                carregarMatriculas(); // Atualizar lista de matrículas
            } else {
                alert("Erro ao editar matrícula.");
            }
        },
        error: function() {
            alert("Erro ao tentar editar a matrícula.");
        }
    });
});

function confirmarExcluir(id) {
    $("#matricula_id").val(id);
    $("#modalExcluir").modal("show");
}
</script>

</body>
</html>


