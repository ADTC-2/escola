<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciamento de Professores</h2>
    <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
        <i class="fas fa-plus-circle"></i> <span><strong>Cadastrar</strong></span>
    </button><br><br>

    <table class="table table-striped" id="tabelaProfessores">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="listaProfessores">
            <!-- Professores serão carregados aqui -->
        </tbody>
    </table>
</div>

<!-- Modal Cadastrar -->
<div class="modal" id="modalCadastrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarProfessor">
                    <div class="mb-3">
                        <label for="usuario_id" class="form-label">Usuário</label>
                        <select class="form-control" id="usuario_id" required>
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
<div class="modal" id="modalEditarProfessor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarProfessor">
                    <input type="hidden" id="idEditar">
                    <div class="mb-3">
                        <label for="usuario_idEditar" class="form-label">Usuário</label>
                        <select class="form-control" id="usuario_idEditar" required>
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
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#tabelaProfessores').DataTable({
            "serverSide": false,
            "ajax": {
                url: '../../controllers/professores.php',
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
                { "data": "usuario_nome" },
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

        function carregarUsuarios(selectedId = '') {
            $.post('../../controllers/usuario.php', { acao: 'listar' }, function(response) {
                if (response.sucesso) {
                    let options = '<option value="">Selecione</option>';
                    response.data.forEach(u => {
                        options += `<option value="${u.id}" ${u.id == selectedId ? 'selected' : ''}>${u.nome}</option>`;
                    });
                    $('#usuario_id').html(options);
                    $('#usuario_idEditar').html(options);
                } else {
                    console.error("Erro ao carregar usuários:", response.mensagem);
                }
            }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição dos usuários:", textStatus, errorThrown);
            });
        }

        carregarUsuarios();

        // Cadastro de Professor
        $("#formCadastrarProfessor").submit(function(e) {
            e.preventDefault();
            $.post('../../controllers/professores.php', {
                acao: 'salvar',
                usuario_id: $('#usuario_id').val()
            }, function(response) {
                alert(response.mensagem);
                if (response.sucesso) {
                    $('#modalCadastrar').modal('hide');
                    table.ajax.reload();
                }
            }, 'json');
        });

        // Editar Professor
        $("#formEditarProfessor").submit(function(e) {
            e.preventDefault();
            $.post('../../controllers/professores.php', {
                acao: 'editar',
                id: $('#idEditar').val(),
                usuario_id: $('#usuario_idEditar').val()
            }, function(response) {
                alert(response.mensagem);
                if (response.sucesso) {
                    $('#modalEditarProfessor').modal('hide');
                    table.ajax.reload();
                }
            }, 'json');
        });

        // Exclusão de Professor
        $('#tabelaProfessores').on('click', '.excluir', function() {
            let id = $(this).data('id');
            if (confirm("Tem certeza que deseja excluir este professor?")) {
                $.post('../../controllers/professores.php', { acao: 'excluir', id: id }, function(response) {
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


