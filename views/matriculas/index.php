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
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function() {
    // Função para carregar dados dinamicamente nos selects
    function carregarDadosSelect(url, targetId, selectedId = '') {
        $.get(url, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(item => {
                    options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.nome}</option>`;
                });
                $(`#${targetId}`).html(options);
            } else {
                console.error("Erro ao carregar dados:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição:", textStatus, errorThrown);
        });
    }

    // Carregar os dados dos selects para Cadastro e Edição
    $.get('../../controllers/matriculas.php', { acao: 'carregarSelects' }, function(response) {
            if (response.sucesso) {
                // Carregar os dados nos selects
                let alunos = response.data.alunos;
                let classes = response.data.classes;
                let congregacoes = response.data.congregacoes;
                let usuarios = response.data.usuarios;

                let alunoOptions = '<option value="">Selecione</option>';
                alunos.forEach(aluno => {
                    alunoOptions += `<option value="${aluno.id}">${aluno.nome}</option>`;
                });
                $('#aluno').html(alunoOptions);

                let classeOptions = '<option value="">Selecione</option>';
                classes.forEach(classe => {
                    classeOptions += `<option value="${classe.id}">${classe.nome}</option>`;
                });
                $('#classe').html(classeOptions);

                let congregacaoOptions = '<option value="">Selecione</option>';
                congregacoes.forEach(congregacao => {
                    congregacaoOptions += `<option value="${congregacao.id}">${congregacao.nome}</option>`;
                });
                $('#congregacao').html(congregacaoOptions);

                let usuarioOptions = '<option value="">Selecione</option>';
                usuarios.forEach(usuario => {
                    usuarioOptions += `<option value="${usuario.id}">${usuario.nome}</option>`;
                });
                $('#professor').html(usuarioOptions);
            } else {
                console.error("Erro ao carregar os selects:", response.mensagem);
            }
        }, 'json');
        $(document).ready(function() {
        // Definindo a função listarMatriculas
        function listarMatriculas() {
            $.get('../../controllers/matriculas.php', { acao: 'listar' }, function(response) {
                if (response.sucesso) {
                    let tabela = '';
                    response.data.forEach(function(matricula) {
                        tabela += `<tr>
                            <td>${matricula.id}</td>
                            <td>${matricula.aluno}</td>
                            <td>${matricula.classe}</td>
                            <td>${matricula.congregacao}</td>
                            <td>${matricula.usuario}</td>
                            <td>${matricula.data}</td>
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
                    console.error('Erro ao listar matrículas:', response.mensagem);
                }
            }, 'json');
        }

        // Agora chamando a função
        listarMatriculas();

        // Outras funções e chamadas do seu código
    });

    // Função para salvar a matrícula
    $('#formCadastrarMatricula').submit(function(e) {
        e.preventDefault();

        let aluno_id = $('#aluno').val();
        let classe_id = $('#classe').val();
        let congregacao_id = $('#congregacao').val();
        let trimestre = $('#trimestre').val();

        if (!aluno_id || !classe_id || !congregacao_id || !trimestre) {
            alert('Todos os campos são obrigatórios!');
            return;
        }

        $.post('../../controllers/matriculas.php', {
            acao: 'cadastrar',
            aluno_id: aluno_id,
            classe_id: classe_id,
            congregacao_id: congregacao_id,
            trimestre: trimestre
        }, function(response) {
            if (response.sucesso) {
                alert(response.mensagem);
                $('#modalCadastrar').modal('hide');
                listarMatriculas(); // Atualiza a lista de matrículas
            } else {
                alert(response.mensagem);
            }
        }, 'json');
    });

    // Função de exclusão da matrícula
    $('#confirmarExcluir').click(function() {
        let matricula_id = $('#matricula_id').val();

        $.post('../../controllers/matriculas.php', {
            acao: 'excluir',
            id: matricula_id
        }, function(response) {
            if (response.sucesso) {
                alert(response.mensagem);
                $('#modalExcluir').modal('hide');
                listarMatriculas(); // Atualiza a lista de matrículas
            } else {
                alert(response.mensagem);
            }
        }, 'json');
    });

    // Função de edição de matrícula
    $('#salvarEdicao').click(function() {
        let matricula_id = $('#matricula_id').val();
        let aluno_id = $('#aluno_editar').val();
        let classe_id = $('#classe_editar').val();
        let congregacao_id = $('#congregacao_editar').val();
        let trimestre = $('#trimestre_editar').val();

        if (!aluno_id || !classe_id || !congregacao_id || !trimestre) {
            alert('Todos os campos são obrigatórios!');
            return;
        }

        $.post('../../controllers/matriculas.php', {
            acao: 'editar',
            id: matricula_id,
            aluno_id: aluno_id,
            classe_id: classe_id,
            congregacao_id: congregacao_id,
            trimestre: trimestre
        }, function(response) {
            if (response.sucesso) {
                alert(response.mensagem);
                $('#modalEditar').modal('hide');
                listarMatriculas(); // Atualiza a lista de matrículas
            } else {
                alert(response.mensagem);
            }
        }, 'json');
    });

    // Função para preencher os campos de edição
    $(document).on('click', '.editar', function() {
        let matricula_id = $(this).data('id');

        $.post('../../controllers/matriculas.php', { acao: 'buscar', id: matricula_id }, function(response) {
            if (response.sucesso) {
                let matricula = response.data;
                $('#matricula_id').val(matricula.id);
                $('#aluno_editar').val(matricula.aluno_id);
                $('#classe_editar').val(matricula.classe_id);
                $('#congregacao_editar').val(matricula.congregacao_id);
                $('#trimestre_editar').val(matricula.trimestre);
                $('#modalEditar').modal('show');
            }
        }, 'json');
    });
});

</script>
</body>
</html>