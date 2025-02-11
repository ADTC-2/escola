<?php require_once '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Gerenciamento de Congregações</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCongregacao">Adicionar Congregação</button>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="listaCongregacoes">
            <!-- As congregações serão inseridas aqui via AJAX -->
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCongregacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Congregação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCongregacao">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="acao" name="acao" value="cadastrar">
                    
                    <div class="mb-3">
                        <label>Nome:</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label>Endereço:</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/ajax.js"></script>
<?php require_once '../includes/footer.php'; ?>
