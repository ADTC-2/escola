<?php
require_once '../config/conexao.php';

header('Content-Type: application/json');

if (isset($_POST['acao'])) {
    switch ($_POST['acao']) {
        case 'listar':
            try {
                $query = "SELECT c.id, c.nome, g.nome AS congregacao_nome 
                          FROM classes c
                          JOIN congregacoes g ON c.congregacao_id = g.id";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['sucesso' => true, 'data' => $dados]);
            } catch (PDOException $e) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao listar classes: ' . $e->getMessage()]);
            }
            break;

        case 'salvar':
            if (isset($_POST['nome']) && isset($_POST['congregacao_id'])) {
                try {
                    $query = "INSERT INTO classes (nome, congregacao_id) VALUES (:nome, :congregacao_id)";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':nome', $_POST['nome']);
                    $stmt->bindParam(':congregacao_id', $_POST['congregacao_id']);
                    $stmt->execute();
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Classe cadastrada com sucesso']);
                } catch (PDOException $e) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar classe: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            }
            break;

        case 'editar':
            if (isset($_POST['id']) && isset($_POST['nome']) && isset($_POST['congregacao_id'])) {
                try {
                    $query = "UPDATE classes SET nome = :nome, congregacao_id = :congregacao_id WHERE id = :id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':nome', $_POST['nome']);
                    $stmt->bindParam(':congregacao_id', $_POST['congregacao_id']);
                    $stmt->bindParam(':id', $_POST['id']);
                    $stmt->execute();
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Classe atualizada com sucesso']);
                } catch (PDOException $e) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao editar classe: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            }
            break;

        case 'excluir':
            if (isset($_POST['id'])) {
                try {
                    $query = "DELETE FROM classes WHERE id = :id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':id', $_POST['id']);
                    $stmt->execute();
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Classe excluída com sucesso']);
                } catch (PDOException $e) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir classe: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
            }
            break;

        case 'buscar':
            if (isset($_POST['id'])) {
                try {
                    $query = "SELECT id, nome, congregacao_id FROM classes WHERE id = :id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':id', $_POST['id']);
                    $stmt->execute();
                    $classe = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($classe) {
                        echo json_encode(['sucesso' => true, 'data' => $classe]);
                    } else {
                        echo json_encode(['sucesso' => false, 'mensagem' => 'Classe não encontrada']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar classe: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
            }
            break;

        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
            break;
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não especificada']);
}
?>