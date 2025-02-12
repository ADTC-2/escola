<?php
require_once "../config/conexao.php";

// Criar classe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'criar') {
    $nome = $_POST['nome'];
    $congregacao_id = $_POST['congregacao_id'];

    $stmt = $pdo->prepare("INSERT INTO classes (nome, congregacao_id) VALUES (?, ?)");
    if ($stmt->execute([$nome, $congregacao_id])) {
        echo "Classe adicionada com sucesso!";
    } else {
        echo "Erro ao adicionar classe.";
    }
}

// Listar classes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'listar') {
    $stmt = $pdo->query("SELECT * FROM classes");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($classes as $classe) {
        echo "<tr>
                <td>{$classe['nome']}</td>
                <td>{$classe['congregacao_id']}</td>
                <td>
                    <button class='btn btn-warning editar' data-id='{$classe['id']}'>Editar</button>
                    <button class='btn btn-danger excluir' data-id='{$classe['id']}'>Excluir</button>
                </td>
              </tr>";
    }
}

// Editar classe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $congregacao_id = $_POST['congregacao_id'];

    $stmt = $pdo->prepare("UPDATE classes SET nome = ?, congregacao_id = ? WHERE id = ?");
    if ($stmt->execute([$nome, $congregacao_id, $id])) {
        echo "Classe editada com sucesso!";
    } else {
        echo "Erro ao editar classe.";
    }
}

// Excluir classe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'excluir') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "Classe excluída com sucesso!";
    } else {
        echo "Erro ao excluir classe.";
    }
}
?>
