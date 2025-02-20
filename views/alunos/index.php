<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Cadastro de Alunos</h2>
    <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
        <i class="fas fa-user-plus"></i> 
    </button>
    <br><br>

    <!-- Tabela DataTables -->
    <div id="tabelaContainer">
        <table id="tabelaAlunos" class="display nowrap table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Nascimento</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Container para exibir alunos como cartões no celular -->
    <div id="cardsContainer" class="d-none"></div>
</div>

<!-- Modal Cadastrar -->
<div class="modal" id="modalCadastrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarAluno">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" name="data_nascimento" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" name="telefone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal" id="modalEditarAluno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarAluno">
                    <input type="hidden" name="id" id="idEditar">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" id="nomeEditar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" name="data_nascimento" id="data_nascimentoEditar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" name="telefone" id="telefoneEditar" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir -->
<div class="modal" id="modalExcluirAluno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este aluno?</p>
                <input type="hidden" id="idExcluir">
                <button type="button" class="btn btn-danger" id="btnConfirmarExcluir">Excluir</button>
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
    let tabela = $('#tabelaAlunos').DataTable({
        ajax: '../../controllers/aluno.php?acao=listar',
        responsive: true,
        columns: [
            { data: 'nome', className: 'text-nowrap' },
            { 
                data: 'data_nascimento',
                className: 'text-nowrap',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString('pt-BR') : '-';
                }
            },
            { data: 'telefone', className: 'text-nowrap' },
            {
                data: 'id',
                render: function(data) {
                    return `
                        <div class="d-flex gap-2">
                            <button class="btn btn-warning btn-sm btnEditar" data-id="${data}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btnExcluir" data-id="${data}">
                                <i class="fas fa-trash-alt"></i> 
                            </button>
                        </div>
                    `;
                },
                className: 'text-center'
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        },
        initComplete: function() {
            atualizarExibicao();
            atribuirEventos();
        }
    });

    function atualizarExibicao() {
        if ($(window).width() < 768) {
            $('#tabelaContainer').hide();
            $('#cardsContainer').removeClass('d-none').html('');

            tabela.ajax.json().data.forEach(aluno => {
                let card = `
                    <div class="card mb-2 shadow-sm p-3">
                        <div class="card-body">
                            <h5 class="card-title">${aluno.nome}</h5>
                            <p><strong>Data de Nascimento:</strong> ${aluno.data_nascimento ? new Date(aluno.data_nascimento).toLocaleDateString('pt-BR') : '-'}</p>
                            <p><strong>Telefone:</strong> ${aluno.telefone}</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-warning btn-sm btnEditar" data-id="${aluno.id}">
                                    <i class="fas fa-edit"></i> 
                                </button>
                                <button class="btn btn-danger btn-sm btnExcluir" data-id="${aluno.id}">
                                    <i class="fas fa-trash-alt"></i> 
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#cardsContainer').append(card);
            });

            atribuirEventos();
        } else {
            $('#tabelaContainer').show();
            $('#cardsContainer').addClass('d-none');
        }
    }

    function atribuirEventos() {
        // Cadastrar Aluno
        $('#formCadastrarAluno').off('submit').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.post('../../controllers/aluno.php?acao=salvar', form.serialize(), function(response) {
                if (response.status === "success") {
                    $('#modalCadastrar').modal('hide');
                    tabela.ajax.reload();
                } else {
                    alert(response.message);
                }
            });
        });

        // Editar Aluno
        $('.btnEditar').off('click').on('click', function() {
            let id = $(this).data('id');
            $.getJSON(`../../controllers/aluno.php?acao=buscar&id=${id}`, function(aluno) {
                $('#idEditar').val(aluno.id);
                $('#nomeEditar').val(aluno.nome);
                $('#data_nascimentoEditar').val(aluno.data_nascimento);
                $('#telefoneEditar').val(aluno.telefone);
                $('#modalEditarAluno').modal('show');
            });
        });

        $('#formEditarAluno').off('submit').on('submit', function(e) {
            e.preventDefault();
            $.post('../../controllers/aluno.php?acao=editar', $(this).serialize(), function(response) {
                if (response.status === "success") {
                    $('#modalEditarAluno').modal('hide');
                    tabela.ajax.reload();
                } else {
                    alert(response.message);
                }
            });
        });

        // Excluir Aluno
        $('.btnExcluir').off('click').on('click', function() {
            let id = $(this).data('id');
            $('#idExcluir').val(id);
            $('#modalExcluirAluno').modal('show');
        });

        $('#btnConfirmarExcluir').off('click').on('click', function() {
            let id = $('#idExcluir').val();
            $.post('../../controllers/aluno.php?acao=excluir', { id: id }, function(response) {
                if (response.status === "success") {
                    $('#modalExcluirAluno').modal('hide');
                    tabela.ajax.reload();
                } else {
                    alert(response.message);
                }
            });
        });
    }

    $(window).resize(atualizarExibicao);
    tabela.on('xhr', function() {
        atualizarExibicao();
        atribuirEventos();
    });
});
</script>

</body>
</html>
