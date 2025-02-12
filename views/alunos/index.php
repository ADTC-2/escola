<?php require_once '../../config/conexao.php'; ?>
<?php require_once '../../header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciar Alunos</h2>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalCadastrar">Cadastrar Aluno</button>

    <!-- Tabela de Alunos -->
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Data de Nascimento</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="lista-alunos">
            <!-- Alunos serão carregados aqui com AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal Cadastrar -->
<div class="modal fade" id="modalCadastrar" tabindex="-1" role="dialog" aria-labelledby="modalCadastrarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCadastrarLabel">Cadastrar Aluno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarAluno">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control" id="telefone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>


