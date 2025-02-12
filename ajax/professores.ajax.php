<?php
require_once "../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao'])) {
    if ($_POST['acao'] == 'listar') {
        $stmt = $pdo->query("SELECT * FROM professores");
        $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($professores as $professor) {
            echo "<tr>
                    <td>{$professor['usuario_id']}</td>
                    <td>{$professor['congregacao_id']}</td>
                    <td>
                        <button class='btn btn-warning editar' data-id='{$professor['id']}'>Editar</button>
                        <button class='btn btn-danger excluir' data-id='{$professor['id']}'>Excluir</button>
                    </td>
                </tr>";
        }
    }

    if ($_POST['acao'] == 'criar') {
        $usuario_id = $_POST['usuario_id'];
        $congregacao_id = $_POST['congregacao_id'];

        $stmt = $pdo->prepare("INSERT INTO professores (usuario_id, congregacao_id) VALUES (?, ?)");
        if ($stmt->execute([$usuario_id, $congregacao_id])) {
            echo "Professor adicionado com sucesso!";
        } else {
            echo "Erro ao adicionar professor.";
        }
    }

    if ($_POST['acao'] == 'editar') {
        $id = $_POST['id'];
        $usuario_id = $_POST['usuario_id'];
        $congregacao_id = $_POST['congregacao_id'];

        $stmt = $pdo->prepare("UPDATE professores SET usuario_id = ?, congregacao_id = ? WHERE id = ?");
        if ($stmt->execute([$usuario_id, $congregacao_id, $id])) {
            echo "Professor editado com sucesso!";
        } else {
            echo "Erro ao editar professor.";
        }
    }

    if ($_POST['acao'] == 'excluir') {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM professores WHERE id = ?");
        if ($stmt->execute([$id])) {
            echo "Professor excluído com sucesso!";
        } else {
            echo "Erro ao excluir professor.";
        }
    }
}
?>
