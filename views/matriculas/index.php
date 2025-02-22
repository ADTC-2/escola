<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciamento de Matrículas</h2>
    <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
        <i class="fas fa-plus-circle"></i> <span><strong>Cadastrar</strong></span>
    </button><br><br>
    <!-- Tabela de Matrículas -->
    <div class="table-responsive">
        <table id="tabelaMatriculas" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aluno</th>
                    <th>Classe</th>
                    <th>Congregação</th>
                    <th>Professor</th>
                    <th>Data</th>
                    <th>Trimestre</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados serão preenchidos via JavaScript -->
            </tbody>
        </table>
    </div>

<!-- Modal de Cadastro de Matrícula -->
<div id="modalCadastrar" class="modal fade" tabindex="-1" aria-labelledby="modalCadastrarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCadastrarLabel">Cadastrar Matrícula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarMatricula">
                    <div class="mb-3">
                        <label for="aluno" class="form-label">Aluno</label>
                        <select id="aluno" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="classe" class="form-label">Classe</label>
                        <select id="classe" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="congregacao" class="form-label">Congregação</label>
                        <select id="congregacao" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="professor" class="form-label">Professor</label>
                        <select id="professor" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="trimestre" class="form-label">Trimestre</label>
                        <input type="number" id="trimestre" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Matrícula -->
<div id="modalEditar" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Matrícula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarMatricula">
                    <input type="hidden" id="matricula_id" name="matricula_id">
                    <div class="form-group">
                        <label for="aluno_editar">Aluno</label>
                        <select class="form-control" id="aluno_editar" name="aluno_editar"></select>
                    </div>
                    <div class="form-group">
                        <label for="classe_editar">Classe</label>
                        <select class="form-control" id="classe_editar" name="classe_editar"></select>
                    </div>
                    <div class="form-group">
                        <label for="congregacao_editar">Congregação</label>
                        <select class="form-control" id="congregacao_editar" name="congregacao_editar"></select>
                    </div>
                    <div class="form-group">
                        <label for="professor_editar">Professor</label>
                        <select class="form-control" id="professor_editar" name="professor_editar"></select>
                    </div>
                    <div class="form-group">
                        <label for="trimestre_editar">Trimestre</label>
                        <input type="number" class="form-control" id="trimestre_editar" name="trimestre_editar">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="salvarEdicao">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="modalExcluir" class="modal fade" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Você tem certeza que deseja excluir esta matrícula?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarExcluir">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function() {
    // Inicializar a tabela
    $('#tabelaMatriculas').DataTable({
        responsive: true
    });

    // Carregar alunos, classes, congregações, professores e matrículas ao iniciar
    carregarAlunos();
    carregarClasses();
    carregarCongregacoes();
    carregarProfessores();
    carregarMatriculas();

    // Função para carregar alunos
    function carregarAlunos(selectedId = '') {
        $.post('../../controllers/matriculas.php', {
            acao: 'listarAlunos'
        }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(a => {
                    options +=
                        `<option value="${a.id}" ${a.id == selectedId ? 'selected' : ''}>${a.nome}</option>`;
                });
                $('#aluno').html(options);
                $('#aluno_editar').html(options);
            } else {
                console.error("Erro ao carregar alunos:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição dos alunos:", textStatus, errorThrown);
        });
    }

    // Função para carregar classes
    function carregarClasses(selectedId = '') {
        $.post('../../controllers/classe.php', {
            acao: 'listar'
        }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(c => {
                    options +=
                        `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
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

    // Função para carregar congregações
    function carregarCongregacoes(selectedId = '') {
        $.post('../../controllers/congregacao.php', {
            acao: 'listar'
        }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(c => {
                    options +=
                        `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
                });
                $('#congregacao').html(options);
                $('#congregacao_editar').html(options);
            } else {
                console.error("Erro ao carregar congregações:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição das congregações:", textStatus, errorThrown);
        });
    }

    // Função para carregar professores
    function carregarProfessores(selectedId = '') {
        $.post('../../controllers/usuario.php', {
            acao: 'listar'
        }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(p => {
                    options +=
                        `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.nome}</option>`;
                });
                $('#professor').html(options);
                $('#professor_editar').html(options);
            } else {
                console.error("Erro ao carregar professores:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição dos professores:", textStatus, errorThrown);
        });
    }

    // Função para carregar matrículas
    function carregarMatriculas() {
        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: {
                acao: "listarMatriculas"
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    let table = $('#tabelaMatriculas').DataTable();
                    table.clear(); // Limpa os dados antes de inserir novos

                    response.matriculas.forEach(matricula => {
                        let dataMatricula = new Date(matricula.data_matricula);
                        dataMatricula.setHours(dataMatricula.getHours() + dataMatricula
                            .getTimezoneOffset() / 60);
                        let dataFormatada =
                            `${("0" + dataMatricula.getDate()).slice(-2)}/${("0" + (dataMatricula.getMonth() + 1)).slice(-2)}/${dataMatricula.getFullYear()}`;

                        table.row.add([
                            matricula.id,
                            matricula.aluno_nome,
                            matricula.classe_nome,
                            matricula.congregacao_nome,
                            matricula.professor_nome,
                            dataFormatada,
                            matricula.trimestre,
                            matricula.status,
                            `
                             <button class="btn btn-danger btn-sm" data-id="${matricula.id}">
                                <i class="fas fa-trash"></i>
                             </button>`
                        ]).draw();
                    });
                } else {
                    alert('Erro ao carregar matrículas.');
                }
            },
            error: function() {
                alert("Erro ao carregar matrículas.");
            }
        });
    }

    // Enviar dados para cadastrar a matrícula
    $("#formCadastrarMatricula").submit(function(e) {
        e.preventDefault();

        let aluno_id = $("#aluno").val();
        let classe_id = $("#classe").val();
        let congregacao_id = $("#congregacao").val();
        let professor_id = $("#professor").val();
        let trimestre = $("#trimestre").val();

        if (!aluno_id || !classe_id || !congregacao_id || !professor_id || !trimestre) {
            alert("Todos os campos devem ser preenchidos!");
            return;
        }

        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: {
                acao: "cadastrarMatricula",
                aluno_id: aluno_id,
                classe_id: classe_id,
                congregacao_id: congregacao_id,
                professor_id: professor_id,
                trimestre: trimestre
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    alert("Matrícula cadastrada com sucesso!");
                    $("#modalCadastrar").modal("hide");
                    carregarMatriculas();
                } else {
                    alert("Erro ao cadastrar matrícula: " + response.mensagem);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição:", textStatus, errorThrown);
                alert("Erro ao tentar cadastrar matrícula.");
            }
        });
    });

    // Função para editar a matrícula
    function editarMatricula(id) {
        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: {
                acao: "buscarMatricula",
                matricula_id: id
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    // Preencher os campos do modal de edição com as informações da matrícula
                    $("#matricula_id").val(response.matricula.id);
                    $("#aluno_editar").val(response.matricula.aluno_id);
                    $("#classe_editar").val(response.matricula.classe_id);
                    $("#congregacao_editar").val(response.matricula.congregacao_id);
                    $("#professor_editar").val(response.matricula.professor_id);
                    $("#trimestre_editar").val(response.matricula.trimestre);

                    // Exibir o modal de edição
                    $("#modalEditar").modal("show");
                } else {
                    alert("Erro ao carregar as informações da matrícula.");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição:", textStatus, errorThrown);
                alert("Erro ao tentar carregar as informações da matrícula.");
            }
        });
    }

    // Enviar dados para editar a matrícula
    $("#formEditarMatricula").submit(function(e) {
        e.preventDefault();

        let matricula_id = $("#matricula_id").val();
        let aluno_id = $("#aluno_editar").val();
        let classe_id = $("#classe_editar").val();
        let congregacao_id = $("#congregacao_editar").val();
        let professor_id = $("#professor_editar").val();
        let trimestre = $("#trimestre_editar").val();

        // Validação básica dos campos
        if (!aluno_id || !classe_id || !congregacao_id || !professor_id || !trimestre) {
            alert("Todos os campos devem ser preenchidos!");
            return;
        }

        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: {
                acao: "editarMatricula",
                matricula_id: matricula_id,
                aluno_id: aluno_id,
                classe_id: classe_id,
                congregacao_id: congregacao_id,
                professor_id: professor_id,
                trimestre: trimestre
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    alert("Matrícula atualizada com sucesso!");
                    $("#modalEditar").modal("hide");
                    carregarMatriculas();
                } else {
                    alert("Erro ao atualizar matrícula: " + response.mensagem);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição:", textStatus, errorThrown);
                alert("Erro ao tentar atualizar matrícula.");
            }
        });
    });

    // Confirmar exclusão
    function confirmarExcluir(id) {
        $("#matricula_id").val(id);
        $("#modalExcluir").modal("show");
    }

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
                    alert("Erro ao excluir matrícula: " + response.mensagem);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição:", textStatus, errorThrown);
                alert("Erro ao tentar excluir matrícula.");
            }
        });
    });

    // Vincular eventos de clique aos botões de editar e excluir
    $(document).on('click', '.btn-primary', function() {
        let matricula_id = $(this).data('id');
        editarMatricula(matricula_id);
    });
    
    $(document).on('click', '.btn-danger', function() {
        let matricula_id = $(this).data('id');
        confirmarExcluir(matricula_id);
    });
});
</script>

</body>

</html>