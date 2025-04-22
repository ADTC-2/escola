<?php

require_once '../../config/conexao.php';

$input = json_decode(file_get_contents('php://input'), true);
$acao = $_GET['acao'] ?? $input['acao'] ?? '';
$id = $_GET['id'] ?? $input['id'] ?? 0;

if ($acao == 'listar') {
    try {
        $sql = "
            SELECT c.id, c.data, cl.nome AS classe_nome, c.oferta_classe, c.total_biblias, c.total_revistas, c.total_visitantes, c.trimestre
            FROM chamadas c
            JOIN classes cl ON c.classe_id = cl.id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $chamadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => $chamadas ? 'success' : 'error',
            'chamadas' => $chamadas ?: [],
            'message' => $chamadas ? '' : 'Nenhuma chamada encontrada'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro na consulta: ' . $e->getMessage()]);
    }
} elseif ($acao == 'deletar') {
    if ((int)$id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM chamadas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Chamada excluída com sucesso']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir chamada: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    }
}
?>




