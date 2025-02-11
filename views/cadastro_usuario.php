<?php
// Inclui o cabeçalho
require_once '../includes/header.php';
require_once '../config/conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);  // Criptografa a senha
    $perfil = $_POST['perfil'];

    // Insere o novo usuário no banco
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha, $perfil]);

    // Redireciona para a lista de usuários
    header('Location: usuarios.php');
    exit();
}
?>

<div class="container mt-4">
    <h2>Cadastrar Novo Usuário</h2>

    <form method="POST" action="cadastro_usuario.php">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil</label>
            <select class="form-select" id="perfil" name="perfil" required>
                <option value="admin">Admin</option>
                <option value="user">Usuário</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
