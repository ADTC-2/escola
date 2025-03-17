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
    <div class="modal fade" id="modalCadastrar" tabindex="-1" aria-labelledby="modalCadastrarLabel" aria-hidden="true">
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
                            <select id="aluno" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="classe" class="form-label">Classe</label>
                            <select id="classe" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="congregacao" class="form-label">Congregação</label>
                            <select id="congregacao" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="professor" class="form-label">Professor</label>
                            <select id="professor" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="trimestre" class="form-label">Trimestre</label>
                            <input type="text" id="trimestre" class="form-control" placeholder="Trimestre">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" class="form-select">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Matrícula -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Matrícula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMatricula">
                        <input type="hidden" id="id_edit">
                        <div class="mb-3">
                            <label for="aluno_edit" class="form-label">Aluno</label>
                            <select id="aluno_edit" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="classe_edit" class="form-label">Classe</label>
                            <select id="classe_edit" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="congregacao_edit" class="form-label">Congregação</label>
                            <select id="congregacao_edit" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="professor_edit" class="form-label">Professor</label>
                            <select id="professor_edit" class="form-select">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="trimestre_edit" class="form-label">Trimestre</label>
                            <input type="text" id="trimestre_edit" class="form-control" placeholder="Trimestre">
                        </div>
                        <div class="mb-3">
                            <label for="status_edit" class="form-label">Status</label>
                            <select id="status_edit" class="form-select">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Exclusão de Matrícula -->
    <div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcluirLabel">Excluir Matrícula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir esta matrícula?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarExcluir">Excluir</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- DataTables Principal -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Tradução para Português -->
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"></script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<script> 
$(document).ready(function() {
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

    function preencherSelect(selector, items) {
        let options = '<option value="">Selecione</option>';
        items.forEach(item => {
            options += `<option value="${item.id}">${item.nome}</option>`;
        });
        $(selector).html(options);
    }

    function listarMatriculas() {
        $.ajax({
            url: '../../controllers/matriculas.php',
            type: 'GET',
            data: { acao: 'listarMatriculas' },
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    let tabela = [];
                    response.dados.forEach(matricula => {
                        tabela.push([
                            matricula.id,
                            matricula.aluno,
                            matricula.classe,
                            matricula.congregacao,
                            matricula.usuario,
                            matricula.trimestre,
                            matricula.status,
                            `<button class="btn btn-danger excluir" data-id="${matricula.id}"><i class="fas fa-trash-alt"></i></button>`
                        ]);
                    });

                    if ($.fn.DataTable.isDataTable('#tabelaMatriculas')) {
                        let tabelaMatriculas = $('#tabelaMatriculas').DataTable();
                        tabelaMatriculas.clear().rows.add(tabela).draw();
                    } else {
                        $('#tabelaMatriculas').DataTable({
                            data: tabela,
                            columns: [
                                { title: "ID" },
                                { title: "Aluno" },
                                { title: "Classe" },
                                { title: "Congregação" },
                                { title: "Usuário" },
                                { title: "Trimestre" },
                                { title: "Status" },
                                { title: "Ações" }
                            ],
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'pdfHtml5',
                                    text: 'Exportar PDF',
                                    title: 'Matrículas',
                                    orientation: 'landscape',
                                    pageSize: 'A4'
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Exportar Excel'
                                }
                            ],
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                            },
                            destroy: true  // Permite reinicializar corretamente
                        });
                    }
                } else {
                    alert(response.mensagem || "Erro ao carregar matrículas.");
                }
            },
            error: function() {
                alert("Erro ao listar matrículas.");
            }
        });
    }
    $('#formCadastrarMatricula').submit(function(e) {
    e.preventDefault();

    let dados = {
        aluno_id: $('#aluno').val(),
        classe_id: $('#classe').val(),
        congregacao_id: $('#congregacao').val(),
        professor_id: $('#professor').val(),
        trimestre: $('#trimestre').val(),
        status: $('#status').val(),
        data_matricula: new Date().toISOString().split('T')[0] // Define a data atual
    };

    if (!dados.aluno_id || !dados.classe_id || !dados.congregacao_id || !dados.professor_id || !dados.trimestre || !dados.status) {
        alert("Todos os campos são obrigatórios.");
        return;
    }

    $.ajax({
        url: '../../controllers/matriculas.php?acao=criarMatricula',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(dados),
        dataType: 'json',
        success: function(response) {
            if (response.sucesso) {
                alert(response.mensagem);
                $('#formCadastrarMatricula')[0].reset();
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalCadastrar'));
                modal.hide();
                listarMatriculas();
            } else {
                alert(response.mensagem || "Erro ao cadastrar matrícula.");
            }
        },
        error: function() {
            alert("Erro ao cadastrar matrícula.");
        }
    });
});

    // Editar Matrícula
    $(document).on('click', '.editar', function() {
    let matricula_id = $(this).data('id');

    $.ajax({
        url: `../../controllers/matriculas.php?acao=buscarMatricula&id=${matricula_id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log(response); // Depuração no console

            if (response.sucesso) {
                const matricula = response.dados;
                $('#id_edit').val(matricula.id);
                $('#aluno_edit').val(matricula.aluno_id);
                $('#classe_edit').val(matricula.classe_id);
                $('#congregacao_edit').val(matricula.congregacao_id);
                $('#professor_edit').val(matricula.usuario_id); // Correção
                $('#trimestre_edit').val(matricula.trimestre);
                $('#status_edit').val(matricula.status);

                $('#modalEditar').modal('show');
            } else {
                alert(response.mensagem || "Erro ao carregar os dados da matrícula.");
            }
        },
        error: function(xhr) {
            console.error("Erro ao editar matrícula:", xhr.responseText);
            alert("Erro ao buscar matrícula.");
        }
    });
});


    // Excluir Matrícula
    $(document).on('click', '.excluir', function() {
        let matricula_id = $(this).data('id');
        $('#modalExcluir').modal('show');
        $('#confirmarExcluir').data('id', matricula_id);
    });

    $('#confirmarExcluir').click(function() {
        let matricula_id = $(this).data('id');
        $.ajax({
            url: `../../controllers/matriculas.php?acao=excluirMatricula&id=${matricula_id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    alert(response.mensagem);
                    listarMatriculas();
                    $('#modalExcluir').modal('hide');
                } else {
                    alert(response.mensagem || "Erro ao excluir matrícula.");
                }
            },
            error: function() {
                alert("Erro ao excluir matrícula.");
            }
        });
    });

    carregarSelects();
    listarMatriculas();
});


</script>

</body>
</html>
