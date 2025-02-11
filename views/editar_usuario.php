<?php
// Inclui o cabeçalho
require_once '../includes/header.php';
require_once '../config/conexao.php';

// Verifica se o ID do usuário foi passado
if (!isset($_GET['id'])) {
    echo "ID de usuário não especificado!";
    exit();
}

// Captura o ID do usuário
$id = $_GET['id'];

// Consulta os dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o usuário não for encontrado
if (!$usuario) {
    echo "Usuário não encontrado!";
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $perfil = $_POST['perfil'];

    // Atualiza os dados do usuário
    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, perfil = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $perfil, $id]);

    // Redireciona para a lista de usuários
    header('Location: usuarios.php');
    exit();
}
?>

<div class="container mt-4">
    <h2>Editar Usuário</h2>

    <form method="POST" action="editar_usuario.php?id=<?= $usuario['id']; ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil</label>
            <select class="form-select" id="perfil" name="perfil" required>
                <option value="admin" <?= $usuario['perfil'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?= $usuario['perfil'] == 'user' ? 'selected' : ''; ?>>Usuário</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
