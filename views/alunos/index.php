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

    <!-- Modal de Cadastro -->
    <div id="modalCadastrar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Aluno</h5>
                </div>
                <div class="modal-body">
                    <form id="formCadastrar">
                        <input type="hidden" id="id" name="id">
                        
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                        
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-control" required>
                        
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" required>
                        
                        <label for="classe_id">Classe</label>
                        <select id="classe_id" name="classe_id" class="form-control" required></select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvar">Gravar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="modalEditar" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atualizar Aluno</h5>
                </div>
                <div class="modal-body">
                    <form id="formEditar">
                        <input type="hidden" id="id_editar" name="id">
                        
                        <label for="nome_editar">Nome</label>
                        <input type="text" id="nome_editar" name="nome_editar" class="form-control" required>
                        
                        <label for="telefone_editar">Telefone</label>
                        <input type="text" id="telefone_editar" name="telefone_editar" class="form-control" required>
                        
                        <label for="data_nascimento_editar">Data de Nascimento</label>
                        <input type="date" id="data_nascimento_editar" name="data_nascimento_editar" class="form-control" required>
                        
                        <label for="classe_id_editar">Classe</label>
                        <select id="classe_id_editar" name="classe_id_editar" class="form-control" required></select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnEditar">Atualizar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<script>
// Função para carregar as classes no modal de cadastro e edição
function carregarClasses() {
    $.ajax({
        url: '../../controllers/aluno.php?acao=listar_classes',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var selectClassesCadastro = $('#classe_id');
                var selectClassesEditar = $('#classe_id_editar');
                
                selectClassesCadastro.empty(); // Limpa o select no modal de cadastro
                selectClassesEditar.empty(); // Limpa o select no modal de edição

                // Adiciona a opção "Selecione" no início
                selectClassesCadastro.append('<option value="">Selecione uma classe</option>');
                selectClassesEditar.append('<option value="">Selecione uma classe</option>');

                // Adiciona as classes ao select
                response.data.forEach(function(classe) {
                    selectClassesCadastro.append('<option value="' + classe.id + '">' + classe.nome + '</option>');
                    selectClassesEditar.append('<option value="' + classe.id + '">' + classe.nome + '</option>');
                });
            } else {
                alert('Erro ao carregar classes: ' + response.message);
            }
        },
        error: function() {
            alert('Erro ao carregar as classes.');
        }
    });
}

// Chama a função de carregar classes ao abrir o modal de cadastro
$('#modalCadastrar').on('show.bs.modal', function () {
    $('#formCadastrar')[0].reset();  // Limpa o formulário
    carregarClasses();  // Carrega as classes para o modal de cadastro
});

// Função para editar aluno
$('#tabelaAlunos').on('click', '.btnEditar', function() {
    var alunoId = $(this).data('id'); // Obtém o ID do aluno para buscar os dados
    $('#modalEditar').data('aluno-id', alunoId).modal('show'); // Passa o ID para o modal de edição
});

// Função para carregar os dados do aluno no modal de edição
$('#modalEditar').on('show.bs.modal', function () {
    var alunoId = $(this).data('aluno-id'); // Obtém o ID do aluno que será editado

    // Faz uma requisição AJAX para buscar os dados do aluno
    $.ajax({
        url: '../../controllers/aluno.php?acao=buscar',
        method: 'GET',
        data: { id: alunoId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Preenche os campos do formulário de edição com os dados recebidos
                $('#id_editar').val(response.data.id);
                $('#nome_editar').val(response.data.nome);
                $('#telefone_editar').val(response.data.telefone);
                $('#data_nascimento_editar').val(response.data.data_nascimento);
                $('#classe_id_editar').val(response.data.classe_id); // Seleciona a classe correta
            } else {
                alert('Erro ao carregar os dados do aluno: ' + response.message);
            }
        },
        error: function() {
            alert('Erro ao carregar os dados do aluno.');
        }
    });

    carregarClasses(); // Carrega as classes no select para o modal de edição
});

// Função para editar o aluno
$('#btnEditar').on('click', function() {
    $.ajax({
        url: '../../controllers/aluno.php?acao=editar',
        method: 'POST',
        data: $('#formEditar').serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.status === "success") {
                $('#modalEditar').modal('hide');  // Fecha o modal
                tabela.ajax.reload(null, false); // Atualiza a tabela sem resetar a paginação
                exibirMensagem('sucesso', 'Aluno atualizado com sucesso');
            } else {
                exibirMensagem('erro', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erro ao atualizar aluno:", error);
            exibirMensagem('erro', 'Erro ao atualizar aluno. Verifique o console para mais detalhes.');
        }
    });
});


// Funções de tabela com DataTables
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

// Funções para atualizar a exibição
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
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-danger btn-sm btnExcluir" data-id="${aluno.id}">
                                <i class="fas fa-trash-alt"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('#cardsContainer').append(card);
        });
    } else {
        $('#cardsContainer').addClass('d-none');
        $('#tabelaContainer').show();
    }
}
function exibirMensagem(tipo, mensagem) {
    let classe = tipo === 'sucesso' ? 'alert-success' : 'alert-danger';
    let alerta = `<div class="alert ${classe} alert-dismissible fade show" role="alert">
                    ${mensagem}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>`;
    
    // Adiciona a mensagem no topo da página
    $('.container').prepend(alerta);
    
    // Remove a mensagem após 5 segundos
    setTimeout(() => { $('.alert').alert('close'); }, 5000);
}

function atribuirEventos() {
    $('#tabelaAlunos').on('click', '.btnEditar', function() {
        var alunoId = $(this).data('id');
        $('#modalEditar').data('aluno-id', alunoId).modal('show');
    });
}
</script>

</body>
</html>

