<?php require_once '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Relatórios de Frequência</h2>

    <form id="formRelatorio">
        <div class="row">
            <div class="col-md-4">
                <label>Classe:</label>
                <select class="form-control" id="classe_id" name="classe_id">
                    <option value="1">Classe Infantil</option>
                    <option value="2">Classe Jovens</option>
                    <option value="3">Classe Adultos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Data Início:</label>
                <input type="date" class="form-control" id="data_inicio" name="data_inicio">
            </div>
            <div class="col-md-3">
                <label>Data Fim:</label>
                <input type="date" class="form-control" id="data_fim" name="data_fim">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </div>
        </div>
    </form>

    <h3 class="mt-4">Resultado</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Presenças</th>
                <th>Faltas</th>
            </tr>
        </thead>
        <tbody id="listaRelatorio"></tbody>
    </table>
</div>

<script src="../assets/js/ajax_relatorio.js"></script>
<?php require_once '../includes/footer.php'; ?>
