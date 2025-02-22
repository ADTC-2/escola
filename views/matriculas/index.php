<?php require_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciamento de Matrículas</h2>
    <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
        <i class="fas fa-plus-circle"></i> <span><strong>Cadastrar</strong></span>
    </button><br><br>

    <!-- Tabela de Matrículas com DataTable -->
    <table class="table table-striped" id="tabelaMatriculas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Aluno</th>
                <th>Classe</th>
                <th>Congregação</th>
                <th>Professor</th>
                <th>Data Matrícula</th>
                <th>Trimestre</th>
                <th>Status</th>                
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="listaMatriculas">
            <!-- Matrículas serão carregadas aqui -->
        </tbody>
    </table>
</div>

<!-- Modal Cadastrar Matrícula -->
<div class="modal" id="modalCadastrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Matrícula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarMatricula">
                    <div class="mb-3">
                        <label for="aluno" class="form-label">Aluno</label>
                        <select class="form-control" id="aluno" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="classe" class="form-label">Classe</label>
                        <select class="form-control" id="classe" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="congregacao" class="form-label">Congregação</label>
                        <select class="form-control" id="congregacao" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="professor" class="form-label">Professor</label>
                        <select class="form-control" id="professor" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="trimestre" class="form-label">Trimestre</label>
                        <select class="form-control" id="trimestre" required>
                            <option value="1">1º Trimestre</option>
                            <option value="2">2º Trimestre</option>
                            <option value="3">3º Trimestre</option>
                            <option value="4">4º Trimestre</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Cadastrar Matrícula</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Matrícula -->
<div class="modal" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Matrícula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarMatricula">
                    <input type="hidden" id="matricula_id">
                    <div class="mb-3">
                        <label for="aluno_editar" class="form-label">Aluno</label>
                        <select class="form-control" id="aluno_editar" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="classe_editar" class="form-label">Classe</label>
                        <select class="form-control" id="classe_editar" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="congregacao_editar" class="form-label">Congregação</label>
                        <select class="form-control" id="congregacao_editar" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="professor_editar" class="form-label">Professor</label>
                        <select class="form-control" id="professor_editar" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="trimestre_editar" class="form-label">Trimestre</label>
                        <select class="form-control" id="trimestre_editar" required>
                            <option value="1">1º Trimestre</option>
                            <option value="2">2º Trimestre</option>
                            <option value="3">3º Trimestre</option>
                            <option value="4">4º Trimestre</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar alterações
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir Matrícula -->
<div class="modal" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Matrícula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta matrícula?</p>
                <button type="button" class="btn btn-danger" id="confirmarExcluir"><i class="fas fa-trash"></i> Excluir</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    // Carregar alunos, classes e congregações ao iniciar
    carregarAlunos();
    carregarClasses();
    carregarCongregacoes();
    carregarProfessores();
    carregarMatriculas();    

    // Função para carregar alunos
    function carregarAlunos(selectedId = '') {
        $.post('../../controllers/matriculas.php', { acao: 'listarAlunos' }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(a => {
                    options += `<option value="${a.id}" ${a.id == selectedId ? 'selected' : ''}>${a.nome}</option>`;
                });
                $('#aluno').html(options);
                $('#aluno_editar').html(options);
            } else {
                console.error("Erro ao carregar alunos:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição dos alunos:", textStatus, errorThrown);
        });
    }

    // Função para carregar classes
    function carregarClasses(selectedId = '') {
        $.post('../../controllers/classe.php', { acao: 'listar' }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(c => {
                    options += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
                });
                $('#classe').html(options);
                $('#classe_editar').html(options);
            } else {
                console.error("Erro ao carregar classes:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição das classes:", textStatus, errorThrown);
        });
    }

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

    // Função para carregar professores
    function carregarProfessores(selectedId = '') {
        $.post('../../controllers/usuario.php', { acao: 'listar' }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(p => {
                    options += `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.nome}</option>`;
                });
                $('#professor').html(options);
                $('#professor_editar').html(options);
            } else {
                console.error("Erro ao carregar professores:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição dos professores:", textStatus, errorThrown);
        });
    }

    // Função para carregar matrículas
    function carregarMatriculas() {
        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: { acao: "listarMatriculas" },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    let table = $('#tabelaMatriculas').DataTable();
                    table.clear(); // Limpa os dados antes de inserir novos
                    
                    response.matriculas.forEach(matricula => {
                        let dataMatricula = new Date(matricula.data_matricula);
                        dataMatricula.setHours(dataMatricula.getHours() + dataMatricula.getTimezoneOffset() / 60);
                        let dataFormatada = `${("0" + dataMatricula.getDate()).slice(-2)}/${("0" + (dataMatricula.getMonth() + 1)).slice(-2)}/${dataMatricula.getFullYear()}`;
                        
                        table.row.add([
                            matricula.id,
                            matricula.aluno_nome,
                            matricula.classe_nome,
                            matricula.congregacao_nome,
                            matricula.professor_nome,
                            dataFormatada,
                            matricula.trimestre,
                            matricula.status,
                            `<button class="btn btn-primary btn-sm" onclick="editarMatricula(${matricula.id})">
                                <i class="fas fa-edit"></i>
                             </button>
                             <button class="btn btn-danger btn-sm" onclick="confirmarExcluir(${matricula.id})">
                                <i class="fas fa-trash"></i>
                             </button>`
                        ]).draw();
                    });
                } else {
                    alert('Erro ao carregar matrículas.');
                }
            },
            error: function() {
                alert("Erro ao carregar matrículas.");
            }
        });
    }

    // Enviar dados para cadastrar a matrícula
    $("#formCadastrarMatricula").submit(function(e) {
        e.preventDefault();

        let aluno_id = $("#aluno").val();
        let classe_id = $("#classe").val();
        let congregacao_id = $("#congregacao").val();
        let professor_id = $("#professor").val();
        let trimestre = $("#trimestre").val();

        if (!aluno_id || !classe_id || !congregacao_id || !professor_id || !trimestre) {
            alert("Todos os campos devem ser preenchidos!");
            return;
        }

        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: { 
                acao: "cadastrarMatricula", 
                aluno_id: aluno_id,  
                classe_id: classe_id,  
                congregacao_id: congregacao_id,
                professor_id: professor_id,
                trimestre: trimestre  
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    alert("Matrícula cadastrada com sucesso!");
                    $("#modalCadastrar").modal("hide");
                    carregarMatriculas(); 
                } else {
                    alert("Erro ao cadastrar matrícula.");
                }
            },
            error: function() {
                alert("Erro ao tentar cadastrar matrícula.");
            }
        });
    });

    // Excluir matrícula
    $("#confirmarExcluir").click(function() {
        let matricula_id = $("#matricula_id").val();
        $.ajax({
            url: "../../controllers/matriculas.php",
            type: "POST",
            data: { 
                acao: "excluirMatricula", 
                matricula_id: matricula_id
            },
            dataType: "json",
            success: function(response) {
                if (response.sucesso) {
                    alert("Matrícula excluída com sucesso!");
                    $("#modalExcluir").modal("hide");
                    carregarMatriculas();
                } else {
                    alert("Erro ao excluir matrícula.");
                }
            },
            error: function() {
                alert("Erro ao tentar excluir matrícula.");
            }
        });
    });

    // Confirmar exclusão
    function confirmarExcluir(id) {
        $("#matricula_id").val(id);
        $("#modalExcluir").modal("show");
    }
});
</script>

</body>
</html>

