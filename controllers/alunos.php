<?php
require_once "../config/conexao.php";

// Criar (Adicionar Aluno)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'criar') {
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];

    $stmt = $pdo->prepare("INSERT INTO alunos (nome, data_nascimento, telefone) VALUES (?, ?, ?)");
    if ($stmt->execute([$nome, $data_nascimento, $telefone])) {
        echo "Aluno adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar aluno.";
    }
}

// Listar Alunos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'listar') {
    $stmt = $pdo->query("SELECT * FROM alunos");
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($alunos as $aluno) {
        echo "<tr>
                <td>{$aluno['nome']}</td>
                <td>{$aluno['data_nascimento']}</td>
                <td>{$aluno['telefone']}</td>
                <td>
                    <button class='btn btn-warning editar' data-id='{$aluno['id']}'>Editar</button>
                    <button class='btn btn-danger excluir' data-id='{$aluno['id']}'>Excluir</button>
                </td>
              </tr>";
    }
}

// Editar Aluno
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];

    $stmt = $pdo->prepare("UPDATE alunos SET nome = ?, data_nascimento = ?, telefone = ? WHERE id = ?");
    if ($stmt->execute([$nome, $data_nascimento, $telefone, $id])) {
        echo "Aluno editado com sucesso!";
    } else {
        echo "Erro ao editar aluno.";
    }
}

// Excluir Aluno
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'excluir') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM alunos WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "Aluno excluído com sucesso!";
    } else {
        echo "Erro ao excluir aluno.";
    }
}
?>
