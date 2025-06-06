<?php require_once '../includes/header.php'; ?>

<div class="container mt-2">    
    <button class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastroEdicao">
        <i class="fas fa-plus-circle"></i> <span><strong>Cadastrar Novo Aluno</strong></span>
    </button>
    <br><br>

    <!-- Tabela DataTables -->
    <div id="tabelaContainer" class="table-responsive">
        <table id="tabelaAlunos" class="table table-bordered table-hover d-none d-md-table" style="width:100%">
            <thead class="table-dark">
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

    <!-- Cartões para telas pequenas -->
    <div id="cartoesContainer" class="row d-md-none"></div>

    <!-- Modal de Cadastro e Edição -->
    <div id="modalCadastroEdicao" class="modal fade" tabindex="-1" aria-labelledby="modalCadastroEdicaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCadastroEdicaoLabel">Cadastrar/Editar Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastroEdicao">
                        <input type="hidden" id="id" name="id">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome completo" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" id="telefone" name="telefone" class="form-control" 
                                placeholder="(XX) XXXXX-XXXX" required>
                            <div class="invalid-feedback">Por favor, insira um telefone válido (XX) XXXXX-XXXX</div>
                        </div>

                        <div class="mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="classe_id" class="form-label">Classe</label>
                            <select id="classe_id" name="classe_id" class="form-control" required>
                                <!-- As opções serão carregadas dinamicamente -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvar">Gravar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<script>
$(document).ready(function() {
    // DataTable
    let tabela = $('#tabelaAlunos').DataTable({
        ajax: {
            url: '../../controllers/aluno.php?acao=listar',
            dataSrc: 'data'
        },
        columns: [
            { data: 'nome' },
            { 
                data: 'data_nascimento',
                render: function(data) {
                    return data ? moment(data).utcOffset(-3).format('DD/MM/YYYY') : '-';
                }
            },
            { data: 'telefone' },
            { data: 'classe' },
            {
                data: 'id',
                render: function(data) {
                    return `
                        <button class="btn btn-warning btn-sm btnEditar" data-bs-toggle="modal" data-bs-target="#modalCadastroEdicao" data-id="${data}">
                            <i class="fas fa-edit"></i> 
                        </button>
                        <button class="btn btn-danger btn-sm btnExcluir" data-id="${data}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Função para carregar as classes no modal de cadastro e edição
    function carregarClasses() {
        $.ajax({
            url: '../../controllers/classe.php',
            method: 'POST',
            dataType: 'json',
            data: { acao: 'listar' },
            success: function(response) {
                if (response.sucesso) {
                    var selectClasses = $('#classe_id');
                    selectClasses.empty();
                    selectClasses.append('<option value="">Selecione uma classe</option>');
                    
                    if (Array.isArray(response.data)) {
                        response.data.forEach(function(classe) {
                            selectClasses.append('<option value="' + classe.id + '">' + classe.nome + '</option>');
                        });
                    }
                } else {
                    exibirMensagem('erro', 'Erro ao carregar classes: ' + response.mensagem);
                }
            },
            error: function() {
                exibirMensagem('erro', 'Erro ao carregar as classes.');
            }
        });
    }

    // Função para renderizar os alunos como cartões (para telas menores)
    function renderizarCartoes(alunos) {
        const container = document.getElementById("cartoesContainer");
        container.innerHTML = "";
        
        if (!Array.isArray(alunos)) return;
        
        alunos.forEach(aluno => {
            const card = document.createElement("div");
            card.className = "col-12 col-md-4 mb-3";
            card.innerHTML = `
                <div class="card shadow-lg border-0 rounded">
                    <div class="card-body">
                        <h5 class="card-title text-primary">${aluno.nome}</h5>
                        <p class="card-text"><strong>Data de Nascimento:</strong> ${aluno.data_nascimento ? moment(aluno.data_nascimento).format('DD/MM/YYYY') : '-'}</p>
                        <p class="card-text"><strong>Telefone:</strong> ${aluno.telefone || '-'}</p>
                        <p class="card-text"><strong>Classe:</strong> ${aluno.classe || '-'}</p>
                        <button class="btn btn-warning btn-sm btnEditar" data-bs-toggle="modal" data-bs-target="#modalCadastroEdicao" data-id="${aluno.id}"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-danger btn-sm btnExcluir" data-id="${aluno.id}"><i class="fas fa-trash-alt"></i> Excluir</button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    // Função para carregar alunos (usada apenas para os cartões)
    function carregarAlunosParaCartoes() {
        $.ajax({
            url: '../../controllers/aluno.php?acao=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderizarCartoes(response.data);
                }
            }
        });
    }

    // Lógica para editar aluno
    $(document).on('click', '.btnEditar', function() {
        var alunoId = $(this).data('id');
        
        $('#modalCadastroEdicaoLabel').text('Editar Aluno');
        
        $.ajax({
            url: '../../controllers/aluno.php?acao=buscar',
            method: 'GET',
            data: { id: alunoId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#id').val(response.data.id);
                    $('#nome').val(response.data.nome);
                    $('#telefone').val(response.data.telefone);
                    $('#data_nascimento').val(response.data.data_nascimento);
                    $('#classe_id').val(response.data.classe_id);
                } else {
                    exibirMensagem('erro', 'Erro ao carregar dados do aluno: ' + response.message);
                }
            },
            error: function() {
                exibirMensagem('erro', 'Erro ao carregar os dados do aluno.');
            }
        });
    });

    // Limpar formulário para novo cadastro
    $('#modalCadastroEdicao').on('show.bs.modal', function(e) {
        if (!$(e.relatedTarget).hasClass('btnEditar')) {
            $('#modalCadastroEdicaoLabel').text('Cadastrar Aluno');
            $('#formCadastroEdicao')[0].reset();
            $('#id').val('');
        }
        carregarClasses();
    });

    // Função para salvar 
    $('#btnSalvar').on('click', function() {
        var nome = $('#nome').val().trim();
        var telefone = $('#telefone').val().trim().replace(/\D/g, '');
        var dataNascimento = $('#data_nascimento').val().trim();
        var classeId = $('#classe_id').val();
        var alunoId = $('#id').val();

        // Validação básica
        if (!nome) {
            exibirMensagem('erro', 'Por favor, informe o nome do aluno.');
            return;
        }
        
        if (!telefone || telefone.length < 10 || telefone.length > 11) {
            exibirMensagem('erro', 'Por favor, insira um telefone válido (10 ou 11 dígitos).');
            return;
        }
        
        if (!dataNascimento) {
            exibirMensagem('erro', 'Por favor, informe a data de nascimento.');
            return;
        }
        
        if (!classeId) {
            exibirMensagem('erro', 'Por favor, selecione uma classe.');
            return;
        }

        var url = alunoId ? '../../controllers/aluno.php?acao=editar' : '../../controllers/aluno.php?acao=salvar';

        $.ajax({
            url: url,
            method: 'POST',
            data: $('#formCadastroEdicao').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === "success") {
                    $('#modalCadastroEdicao').modal('hide');
                    tabela.ajax.reload(null, false);
                    carregarAlunosParaCartoes();
                    exibirMensagem('sucesso', response.message);
                } else {
                    exibirMensagem('erro', response.message);
                }
            },
            error: function() {
                exibirMensagem('erro', 'Erro ao comunicar com o servidor.');
            }
        });
    });

    // Função para excluir aluno
    $(document).on('click', '.btnExcluir', function() {
        var alunoId = $(this).data('id');

        if (confirm('Você tem certeza que deseja excluir este aluno?')) {
            $.ajax({
                url: '../../controllers/aluno.php?acao=excluir',
                method: 'POST',
                data: { id: alunoId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === "success") {
                        tabela.ajax.reload(null, false);
                        carregarAlunosParaCartoes();
                        exibirMensagem('sucesso', response.message);
                    } else {
                        exibirMensagem('erro', response.message);
                    }
                },
                error: function() {
                    exibirMensagem('erro', 'Erro ao comunicar com o servidor.');
                }
            });
        }
    });

    // Função para exibir mensagens
    function exibirMensagem(tipo, mensagem) {
        let classe = tipo === 'sucesso' ? 'alert-success' : 'alert-danger';
        let alerta = `
            <div class="alert ${classe} alert-dismissible fade show" role="alert">
                ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('.alert').remove();
        $('.container').prepend(alerta);
        
        setTimeout(() => { $('.alert').alert('close'); }, 5000);
    }

    // Máscara e validação do telefone
    $('#telefone').on('input', function(e) {
        let value = $(this).val().replace(/\D/g, '');
        let formattedValue = '';
        
        if (value.length > 0) {
            formattedValue = '(' + value.substring(0, 2);
        }
        if (value.length > 2) {
            formattedValue += ') ' + value.substring(2, 7);
        }
        if (value.length > 7) {
            formattedValue += '-' + value.substring(7, 11);
        }
        
        $(this).val(formattedValue);
        
        // Validação em tempo real
        if (value.length >= 10 && value.length <= 11) {
            $(this).removeClass('is-invalid');
        } else {
            $(this).addClass('is-invalid');
        }
    });

    // Converter nome para maiúsculas
    $('#nome').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Carregar dados iniciais
    carregarAlunosParaCartoes();
});
</script>

</body>
</html>
<?php require_once '../includes/footer.php'; ?>