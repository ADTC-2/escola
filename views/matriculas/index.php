<?php
require_once '../includes/header.php'; 
?>

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
                    <!-- Campo Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select" required>
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>
                    <div id="feedbackCadastrar" class="text-danger"></div>  <!-- Feedback visual -->
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
                    <!-- Campo Status -->
                    <div class="form-group">
                        <label for="status_editar">Status</label>
                        <select class="form-control" id="status_editar" name="status_editar">
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>
                    <div id="feedbackEditar" class="text-danger"></div>  <!-- Feedback visual -->
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
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<script> 
$(document).ready(function() {
    // Função para carregar os selects de aluno, classe, congregação, e professor
    function carregarSelects() {
        $.ajax({
            url: '../../controllers/matriculas.php',
            type: 'GET',
            data: { acao: 'carregarSelects' },
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    preencherSelect('#aluno', response.dados.alunos);
                    preencherSelect('#classe', response.dados.classes);
                    preencherSelect('#congregacao', response.dados.congregacoes);
                    preencherSelect('#professor', response.dados.usuarios);
                    preencherSelect('#aluno_edit', response.dados.alunos);
                    preencherSelect('#classe_edit', response.dados.classes);
                    preencherSelect('#congregacao_edit', response.dados.congregacoes);
                    preencherSelect('#professor_edit', response.dados.usuarios);
                } else {
                    alert(response.mensagem || "Erro ao carregar dados.");
                }
            },
            error: function() {
                alert("Erro ao carregar os dados.");
            }
        });
    }

    // Preencher o select com os dados recebidos
    function preencherSelect(selector, items) {
        let options = '<option value="">Selecione</option>';
        items.forEach(item => {
            options += `<option value="${item.id}">${item.nome}</option>`;
        });
        $(selector).html(options);
    }

    // Listar Matrículas
    function listarMatriculas() {
        $.ajax({
            url: '../../controllers/matriculas.php',
            type: 'GET',
            data: { acao: 'listarMatriculas' },
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    let tabela = '';
                    response.dados.forEach(matricula => {
                        tabela += `<tr>
                            <td>${matricula.id}</td>
                            <td>${matricula.aluno}</td>
                            <td>${matricula.classe}</td>
                            <td>${matricula.congregacao}</td>
                            <td>${matricula.usuario}</td>
                            <td>${matricula.trimestre}</td>
                            <td>${matricula.status}</td>
                            <td>
                                <button class="btn btn-warning editar" data-id="${matricula.id}">Editar</button>
                                <button class="btn btn-danger excluir" data-id="${matricula.id}">Excluir</button>
                            </td>
                        </tr>`;
                    });
                    $('#tabelaMatriculas tbody').html(tabela);
                } else {
                    alert(response.mensagem || "Erro ao carregar matrículas.");
                }
            },
            error: function() {
                alert("Erro ao listar matrículas.");
            }
        });
    }

// Função para cadastrar matrícula
$('#formCadastrarMatricula').submit(function(e) {
    e.preventDefault();

    let dados = {
        aluno_id: $('#aluno').val(),
        classe_id: $('#classe').val(),
        congregacao_id: $('#congregacao').val(),
        professor_id: $('#professor').val(), // Adicionado professor_id
        trimestre: $('#trimestre').val(),
        status: $('#status').val()  // Adiciona o campo status
    };

    console.log("Dados enviados:", dados);  // Log para depuração

    // Validação básica
    if (!dados.aluno_id || !dados.classe_id || !dados.congregacao_id || !dados.professor_id || !dados.trimestre || !dados.status) {
        alert("Todos os campos são obrigatórios.");
        return;
    }

    $.ajax({
        url: '../../controllers/matriculas.php?acao=criarMatricula',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(dados),
        dataType: 'json',  // Define que espera JSON na resposta
        success: function(response) {
            console.log("Resposta do servidor:", response);  // Verificar resposta do servidor
            if (response.sucesso) {
                alert(response.mensagem);
                $('#formCadastrarMatricula')[0].reset();  // Limpa o formulário após sucesso
                listarMatriculas();  // Atualiza a lista de matrículas
            } else {
                alert(response.mensagem || "Erro ao cadastrar matrícula.");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Erro AJAX:", textStatus, errorThrown);
            alert('Erro ao cadastrar matrícula.');
        }
    });
});

    // Editar Matrícula
    $(document).on('click', '.editar', function() {
        let matricula_id = $(this).data('id');
        $.ajax({
            url: '../../controllers/matriculas.php',
            type: 'GET',
            data: { acao: 'carregarEdicao', id: matricula_id },
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    let matricula = response.dados;
                    $('#matricula_id').val(matricula.id);
                    $('#aluno_edit').val(matricula.aluno_id);
                    $('#classe_edit').val(matricula.classe_id);
                    $('#congregacao_edit').val(matricula.congregacao_id);
                    $('#professor_edit').val(matricula.usuario_id);
                    $('#trimestre_edit').val(matricula.trimestre);
                    $('#status_edit').val(matricula.status);
                    $('#modalEditar').modal('show');
                } else {
                    alert(response.mensagem || "Erro ao carregar dados para edição.");
                }
            },
            error: function() {
                alert("Erro ao carregar os dados de edição.");
            }
        });
    });

// Atualizar Matrícula
$('#formEditarMatricula').submit(function(e) {
    e.preventDefault();

    let matricula_id = $('#matricula_id').val();
    let dados = {
        aluno_id: $('#aluno_edit').val(),
        classe_id: $('#classe_edit').val(),
        professor_id: $('#professor_edit').val(),
        congregacao_id: $('#congregacao_edit').val(),
        trimestre: $('#trimestre_edit').val(),
        status: $('#status_edit').val()
    };

    // Validação antes de enviar
    if (!dados.aluno_id || !dados.classe_id || !dados.congregacao_id || !dados.trimestre || !dados.status) {
        alert("Todos os campos são obrigatórios.");
        return;
    }

    $.ajax({
        url: `../../controllers/matriculas.php?acao=atualizarMatricula&id=${matricula_id}`,
        type: 'PUT',  // O controller espera um PUT
        contentType: 'application/json',
        data: JSON.stringify(dados),
        dataType: 'json',
        success: function(response) {
            if (response.sucesso) {
                alert(response.mensagem);
                $('#modalEditar').modal('hide');
                listarMatriculas();
            } else {
                alert(response.mensagem || "Erro desconhecido");
            }
        },
        error: function() {
            alert("Erro ao editar matrícula.");
        }
    });
});


// Excluir Matrícula
$(document).on('click', '.excluir', function() {
    let matricula_id = $(this).data('id');

    if (confirm('Tem certeza que deseja excluir esta matrícula?')) {
        $.ajax({
            url: `../../controllers/matriculas.php?acao=excluirMatricula&id=${matricula_id}`,
            type: 'GET',  // O controller espera um GET
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    alert(response.mensagem);
                    listarMatriculas();
                } else {
                    alert(response.mensagem || "Erro desconhecido");
                }
            },
            error: function() {
                alert("Erro ao excluir matrícula.");
            }
        });
    }
});

    // Inicializar a página
    carregarSelects();
    listarMatriculas();
});
</script>

</body>
</html>