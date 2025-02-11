<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

$classe = new Classe($pdo);

if (isset($_GET['id'])) {
    $classe_info = $classe->getClasse($_GET['id']);
}
?>

<div class="container mt-4">
    <h2>Editar Classe</h2>

    <form method="POST" action="../controllers/classes.php">
        <input type="hidden" name="id" value="<?= $classe_info['id']; ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Classe</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= $classe_info['nome']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="professor_id" class="form-label">Professor</label>
            <select class="form-select" id="professor_id" name="professor_id" required>
                <option value="<?= $classe_info['professor_id']; ?>">Professor Atual</option>
                <!-- Aqui você pode preencher com os professores cadastrados -->
            </select>
        </div>
        <button type="submit" class="btn btn-warning">Atualizar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
