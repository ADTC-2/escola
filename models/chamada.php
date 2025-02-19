<?php
require_once '../config/conexao.php';

class Chamada {
    public static function getClasses() {
        global $pdo;
        $stmt = $pdo->query("SELECT id, nome FROM classes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAlunos($classeId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT a.id, a.nome 
                               FROM matriculas m 
                               JOIN alunos a ON m.aluno_id = a.id 
                               WHERE m.classe_id = :classeId");
        $stmt->bindParam(':classeId', $classeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function salvar($data) {
        global $pdo;
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO chamadas (data, classe_id, professor_id) VALUES (:data, :classe, :professor)");
            $stmt->execute([
                ':data' => $data['data'],
                ':classe' => $data['classe'],
                ':professor' => $data['professor']
            ]);
            $chamadaId = $pdo->lastInsertId();

            $stmtAluno = $pdo->prepare("INSERT INTO chamada_alunos (chamada_id, aluno_id, presente) VALUES (:chamadaId, :alunoId, :presente)");
            foreach ($data['alunos'] as $aluno) {
                $stmtAluno->execute([
                    ':chamadaId' => $chamadaId,
                    ':alunoId' => $aluno['id'],
                    ':presente' => $aluno['presente']
                ]);
            }

            $pdo->commit();
            return "Chamada registrada com sucesso!";
        } catch (Exception $e) {
            $pdo->rollBack();
            return "Erro ao salvar chamada: " . $e->getMessage();
        }
    }
}
?>




