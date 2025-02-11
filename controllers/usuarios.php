<?php
require_once '../config/conexao.php';
require_once '../models/Usuario.php';

$usuario = new Usuario($pdo);

// Verifica se a ação foi definida
$acao = $_GET['acao'] ?? null;

try {
    switch ($acao) {
        // Caso para Excluir usuário
        case 'excluir':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception("ID inválido.");
            }

            $id = (int) $_GET['id'];
            // Usando o método excluir do modelo
            $usuario->excluir($id);
            
            header('Location: ../views/usuarios.php?sucesso=excluido');
            exit();

        // Caso para Atualizar usuário
        case 'atualizar':
            if (!isset($_POST['id']) || !isset($_POST['nome']) || !isset($_POST['email'])) {
                throw new Exception("Todos os campos são obrigatórios.");
            }

            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'] ?? null;  // Senha pode ser opcional para atualização
            $perfil = $_POST['perfil'] ?? 'user';

            // Usando o método atualizar do modelo
            $usuario->atualizar($id, $nome, $email, $senha, $perfil);

            header('Location: ../views/usuarios.php?sucesso=atualizado');
            exit();

        // Caso para Registrar usuário
        case 'registrar':
            if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha'])) {
                throw new Exception("Todos os campos são obrigatórios.");
            }

            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $perfil = $_POST['perfil'] ?? 'user';

            // Usando o método registrar do modelo
            $usuario->registrar($nome, $email, $senha, $perfil);

            header('Location: ../views/usuarios.php?sucesso=registrado');
            exit();

        // Caso para Listar usuários
        case 'listar':
            $usuarios = $usuario->listar();
            // Aqui você pode passar a lista de usuários para a visão (por exemplo, uma página HTML)
            include '../views/listar_usuarios.php'; // Exemplo de inclusão de uma página para exibir os usuários
            exit();

        // Caso para Buscar usuário por ID
        case 'buscar':
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception("ID inválido.");
            }

            $id = (int) $_GET['id'];
            $usuarioInfo = $usuario->buscarPorId($id);
            
            if (!$usuarioInfo) {
                throw new Exception("Usuário não encontrado.");
            }

            // Aqui você pode exibir as informações do usuário (por exemplo, em uma página de detalhes)
            include '../views/detalhes_usuario.php'; // Exemplo de inclusão de uma página para exibir detalhes do usuário
            exit();

        // Caso para Ação inválida
        default:
            throw new Exception("Ação inválida ou não especificada.");
    }
} catch (Exception $e) {
    // Em caso de erro, redireciona com a mensagem de erro
    header("Location: ../views/usuarios.php?erro=" . urlencode($e->getMessage()));
    exit();
}
?>

