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
} elseif ($acao == 'editar') {
    if ((int)$id > 0) {
        try {
            $sql = "SELECT c.id, c.data, c.classe_id, c.oferta_classe, c.total_biblias, c.total_revistas, c.total_visitantes, c.trimestre
                    FROM chamadas c WHERE c.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $chamada = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => $chamada ? 'success' : 'error',
                'chamada' => $chamada ?: [],
                'message' => $chamada ? '' : 'Chamada não encontrada'
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao buscar chamada: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    }
} elseif ($acao == 'atualizar') {
    if ((int)$id > 0) {
        $data = $input['data'] ?? '';
        $classe_id = $input['classe_id'] ?? '';
        $oferta_classe = $input['oferta_classe'] ?? '';
        $total_biblias = $input['total_biblias'] ?? 0;
        $total_revistas = $input['total_revistas'] ?? 0;
        $total_visitantes = $input['total_visitantes'] ?? 0;
        $trimestre = $input['trimestre'] ?? '';

        if ($data && $classe_id && $oferta_classe && $total_biblias >= 0 && $total_revistas >= 0 && $total_visitantes >= 0 && $trimestre) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE chamadas SET data = :data, classe_id = :classe_id, oferta_classe = :oferta_classe, 
                    total_biblias = :total_biblias, total_revistas = :total_revistas, total_visitantes = :total_visitantes, 
                    trimestre = :trimestre WHERE id = :id
                ");
                $stmt->bindParam(':data', $data);
                $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
                $stmt->bindParam(':oferta_classe', $oferta_classe);
                $stmt->bindParam(':total_biblias', $total_biblias, PDO::PARAM_INT);
                $stmt->bindParam(':total_revistas', $total_revistas, PDO::PARAM_INT);
                $stmt->bindParam(':total_visitantes', $total_visitantes, PDO::PARAM_INT);
                $stmt->bindParam(':trimestre', $trimestre);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Chamada atualizada com sucesso']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar chamada: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos ou incompletos']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    }
}
?>





