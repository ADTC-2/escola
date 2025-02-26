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
    // Função para carregar congregações
    function carregarCongregacoes(selectedId = '') {
        $.post('../../controllers/congregacao.php', { acao: 'listar' }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(c => {
                    options += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
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

    // Carregar as classes ao selecionar a congregação
    $('#congregacao').change(function() {
        let congregacaoId = $(this).val();
        if (congregacaoId) {
            $.ajax({
                url: '../../controllers/matriculas.php',
                type: 'POST',
                data: { congregacao_id: congregacaoId },
                success: function(response) {
                    if (response.sucesso && response.data) {
                        let options = '<option value="">Selecione a Classe</option>';
                        response.data.forEach(function(classe) {
                            options += `<option value="${classe.id}">${classe.nome}</option>`;
                        });
                        $("#classe").html(options).prop('disabled', false);
                    } else {
                        console.error("Nenhuma classe encontrada ou erro na resposta:", response);
                        $("#classe").html('<option value="">Nenhuma classe disponível</option>').prop('disabled', true);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    let errorMsg = "Erro ao carregar classes";
                    if (jqXHR.responseJSON && jqXHR.responseJSON.mensagem) {
                        errorMsg = jqXHR.responseJSON.mensagem;
                    }
                    console.error(errorMsg, textStatus, errorThrown);
                    $("#classe").html('<option value="">' + errorMsg + '</option>').prop('disabled', true);
                }
            });
        } else {
            $("#classe").prop('disabled', true).html('<option value="">Selecione a Classe</option>');
        }
    });

    // Carregar alunos ao selecionar a classe
    $('#classe').change(function() {
        let classeId = $(this).val();
        if (classeId) {
            $.ajax({
                url: `../../controllers/classe.php/${classeId}`,
                type: 'GET',
                success: function(response) {
                    if (response.sucesso) {
                        let table = `<table class="table table-striped">
                                        <thead><tr><th>Nome</th><th>Presente</th></tr></thead><tbody>`;
                        response.data.forEach(function(aluno) {
                            table += `<tr><td>${aluno.nome}</td><td><input type="checkbox" class="aluno-presenca" data-id="${aluno.id}"></td></tr>`;
                        });
                        table += `</tbody></table>`;
                        $("#alunos-container").html(table);
                    } else {
                        console.error("Erro ao carregar alunos:", response.mensagem);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Erro na requisição dos alunos:", textStatus, errorThrown);
                }
            });
        } else {
            $("#alunos-container").empty();
        }
    });

    // Salvar chamada
    $('#formChamada').submit(function(e) {
        e.preventDefault();

        let congregacaoId = $('#congregacao').val();
        let classeId = $('#classe').val();
        let dataChamada = $('#data_chamada').val();
        let professorId = $('#professor_id').val();
        let presencas = [];

        // Validação de campos obrigatórios
        if (!congregacaoId || !classeId || !dataChamada || !professorId) {
            alert('Por favor, preencha todos os campos!');
            return;
        }

        $(".aluno-presenca:checked").each(function() {
            presencas.push({
                aluno_id: $(this).data('id'),
                presente: true
            });
        });

        // Enviar dados para salvar a chamada
        $.ajax({
            url: '../../controllers/chamada.php',
            type: 'POST',
            data: {
                congregacao_id: congregacaoId,
                classe_id: classeId,
                data_chamada: dataChamada,
                professor_id: professorId,
                presencas: presencas
            },
            success: function(response) {
                if (response.sucesso) {
                    alert('Chamada salva com sucesso!');
                    window.location.href = "/dashboard";
                } else {
                    alert('Erro ao salvar a chamada.');
                    console.error("Erro ao salvar a chamada:", response.mensagem);
                }
            },
            error: function() {
                alert('Erro ao salvar a chamada.');
            }
        });
    });

    // Inicializar carregando as congregações
    carregarCongregacoes();
});
</script>
</body>
</html>