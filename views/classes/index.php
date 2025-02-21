<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciamento de Classes</h2>
    <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
    <i class="fas fa-plus-circle"></i> <span><strong>Cadastrar</strong></span>
    </button><br><br>

    <table class="table table-striped" id="tabelaClasses">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="listaClasses">
            <!-- Classes serão carregadas aqui -->
        </tbody>
    </table>
</div>

<!-- Modal Cadastrar -->
<div class="modal" id="modalCadastrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarClasse">
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
<div class="modal" id="modalEditarClasse" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarClasse">
                    <input type="hidden" id="idEditar">
                    <div class="mb-3">
                        <label for="nomeEditar" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeEditar" required>
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
        var table = $('#tabelaClasses').DataTable({
            "serverSide": false,
            "ajax": {
                url: '../../controllers/classe.php',
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
                { "data": "nome" },
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

        $(document).on('submit', '#formCadastrarClasse', function(e) {
            e.preventDefault();
            $.post('../../controllers/classe.php', {
                acao: 'salvar',
                nome: $('#nome').val()
            }, function(response) {
                alert(response.mensagem);
                if (response.sucesso) {
                    $('#modalCadastrar').modal('hide');
                    table.ajax.reload();
                }
            }, 'json');
        });

        $(document).on('click', '.editar', function() {
            let id = $(this).data('id');
            $.post('../../controllers/classe.php', { acao: 'buscar', id: id }, function(response) {
                if (response.sucesso) {
                    $('#idEditar').val(response.data.id);
                    $('#nomeEditar').val(response.data.nome);
                    $('#modalEditarClasse').modal('show');
                } else {
                    alert(response.mensagem);
                }
            }, 'json');
        });

        $(document).on('click', '.excluir', function() {
            let id = $(this).data('id');
            if (confirm("Tem certeza que deseja excluir esta classe?")) {
                $.post('../../controllers/classe.php', { acao: 'excluir', id: id }, function(response) {
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
