<?php
require_once '../config/conexao.php';
require_once '../models/Usuario.php';

$usuario = new Usuario($pdo);
$acao = $_GET['acao'] ?? null;

try {
    if ($acao === 'excluir') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception("ID inválido.");
        }

        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: ../views/usuarios.php?sucesso=excluido');
        exit();
    }
    
    throw new Exception("Ação inválida ou não especificada.");
} catch (Exception $e) {
    header("Location: ../views/usuarios.php?erro=" . urlencode($e->getMessage()));
    exit();
}
?>

<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Gerenciar Usuários</h2>
    <a href="cadastro_usuario.php" class="btn btn-primary mb-3">Cadastrar Novo Usuário</a>
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['perfil']) ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../controllers/usuarios.php?acao=excluir&id=<?= $usuario['id'] ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Tem certeza que deseja excluir?')">
                            Excluir
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
