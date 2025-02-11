<?php
// Inclua o arquivo de conexão com o banco de dados
require_once '../config/conexao.php';
require_once '../includes/header.php';

// Certifique-se de que a variável $pdo foi inicializada com sucesso
if (!$pdo) {
    die("Erro ao conectar ao banco de dados.");
}
?>

<div class="container mt-4">
    <h2>Gerenciar Classes</h2>

    <!-- Exibindo classes -->
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Professor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Agora o PDO estará disponível e podemos executar a consulta
            $stmt = $pdo->query("SELECT * FROM classes");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['nome'] . "</td>";
                // Para simplificação, não estamos associando professores neste exemplo
                echo "<td>" . $row['professor_id'] . "</td>";
                echo "<td>
                    <a href='editar_classe.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a>
                    <a href='../controllers/classes.php?acao=excluir&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>Excluir</a>
                </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Formulário para cadastro -->
    <h3>Cadastrar Nova Classe</h3>
    <form method="POST" action="../controllers/classes.php">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Classe</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="professor_id" class="form-label">Professor</label>
            <select class="form-select" id="professor_id" name="professor_id" required>
                <option value="">Selecione o Professor</option>
                <!-- Aqui você pode preencher com os professores cadastrados -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>

