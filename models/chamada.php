<?php
require_once '../config/conexao.php';

class Chamada {
    public static function getClasses() {
        global $pdo;
        $stmt = $pdo->query("SELECT id, nome FROM classes");
        $options = "<option value=''>Escolha uma classe...</option>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $options .= "<option value='{$row['id']}'>{$row['nome']}</option>";
        }
        return $options;
    }

    public static function getAlunos($classeId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT a.id, a.nome 
                               FROM matriculas m 
                               JOIN alunos a ON m.aluno_id = a.id 
                               WHERE m.classe_id = :classeId");
        $stmt->bindParam(':classeId', $classeId, PDO::PARAM_INT);
        $stmt->execute();

        $html = "<table class='table table-striped'><tr><th>Nome</th><th>Presente</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $html .= "<tr>
                        <td>{$row['nome']}</td>
                        <td><input type='checkbox' class='aluno-presenca' data-id='{$row['id']}'></td>
                      </tr>";
        }
        $html .= "</table>";
        return $html;
    }

    public static function salvar($data) {
        global $pdo;
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO chamadas (data, classe_id) VALUES (:data, :classe)");
            $stmt->execute([
                ':data' => $data['data'],
                ':classe' => $data['classe']
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

