<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Cadastro de Alunos</h2>
    <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
       <i class="fas fa-plus-circle"></i> <span><strong>Cadastrar</strong></span>
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
                    <th>Classe</th>
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
                    <div class="mb-3">
                        <label class="form-label">Classe</label>
                        <select class="form-control" name="classe_id" id="classeCadastrar" required></select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cadastrar
                    </button>
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
                    <div class="mb-3">
                        <label class="form-label">Classe</label>
                        <select class="form-control" name="classe_id" id="classeEditar" required></select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
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
                <button type="button" class="btn btn-danger" id="btnConfirmarExcluir">
                    <i class="fas fa-trash-alt"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Adicionando Font Awesome -->



<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<script>
function carregarClasses() {
    // Exibe um spinner enquanto as classes estão sendo carregadas
    $('#classeCadastrar, #classeEditar').html('<option>Carregando...</option>');
    
    $.getJSON('../../controllers/aluno.php?acao=listar', function(response) {
        console.log(response);  // Verifique a resposta no console
        if (response.status === "error") {
            alert(response.message);
            return;
        }

        if (Array.isArray(response.data)) {
            let options = '<option value="">Selecione a Classe</option>';
            response.data.forEach(classe => {
                options += `<option value="${classe.id}">${classe.nome}</option>`;
            });

            $('#classeCadastrar, #classeEditar').html(options);
        } else {
            alert("Erro ao carregar classes: Estrutura de dados inválida.");
        }
    }).fail(function() {
        alert("Erro ao comunicar com o servidor.");
    });
}

carregarClasses();

let tabela = $('#tabelaAlunos').DataTable({
    ajax: {
        url: '../../controllers/aluno.php?acao=listar',
        dataSrc: 'data',
        error: function(xhr, error, code) {
            console.log("Erro ao carregar os dados da tabela:", error, code);
        }
    },
    responsive: true,
    columns: [
        { data: 'nome' },
        { 
            data: 'data_nascimento',
            render: function(data) {
                return data ? new Date(data).toLocaleDateString('pt-BR') : '-';
            }
        },
        { data: 'telefone' },
        { data: 'classe' },
        {
            data: 'id',
            render: function(data) {
                return `
                    <button class="btn btn-warning btn-sm btnEditar" data-id="${data}">
                        <i class="fas fa-edit"></i> 
                    </button>
                    <button class="btn btn-danger btn-sm btnExcluir" data-id="${data}">
                        <i class="fas fa-trash-alt"></i>
                    </button>`;
            }
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
    $('#formCadastrarAluno').on('submit', function(e) {
        e.preventDefault();
        $.post('../../controllers/aluno.php?acao=salvar', $(this).serialize(), function(response) {
            if (response.status === "success") {
                $('#modalCadastrar').modal('hide');
                tabela.ajax.reload();
                exibirMensagem('sucesso', 'Aluno cadastrado com sucesso');
            } else {
                exibirMensagem('erro', response.message);
            }
        });
    });

    // Editar Aluno
    $('.btnEditar').off('click').on('click', function() {
        let id = $(this).data('id');
        
        $(this).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.getJSON(`../../controllers/aluno.php?acao=buscar&id=${id}`, function(aluno) {
            $('#idEditar').val(aluno.id);
            $('#nomeEditar').val(aluno.nome);
            $('#data_nascimentoEditar').val(aluno.data_nascimento);
            $('#telefoneEditar').val(aluno.telefone);
            $('#classeEditar').val(aluno.classe_id);
            $('#modalEditarAluno').modal('show');
            
            $('.btnEditar').html('<i class="fas fa-edit"></i> Editar');
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
                exibirMensagem('sucesso', 'Aluno excluído com sucesso');
            } else {
                exibirMensagem('erro', response.message);
            }
        });
    });
}

function exibirMensagem(tipo, mensagem) {
    const tipoClasse = tipo === 'sucesso' ? 'alert-success' : 'alert-danger';
    const mensagemHTML = `<div class="alert ${tipoClasse} alert-dismissible fade show" role="alert">
                            ${mensagem}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                          </div>`;
    $('#mensagensContainer').html(mensagemHTML);
}

$(window).resize(atualizarExibicao);
tabela.on('xhr', function() {
    atualizarExibicao();
    atribuirEventos();
});
</script>



</body>

</html>