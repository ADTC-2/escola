<?php
// Inclua o arquivo de configuração e conexão com o banco de dados
include_once('../config/conexao.php');

class Chamada {

    // Método para buscar todas as congregações
    public function getCongregacoes() {
        global $pdo;
        $query = "SELECT * FROM congregacoes";
        $stmt = $pdo->query($query);
        $congregacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $congregacoes;
    }

    // Método para buscar as classes por congregação
    public function getClassesByCongregacao($congregacao_id) {
        global $pdo;
        $query = "SELECT * FROM classes WHERE congregacao_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(1, $congregacao_id, PDO::PARAM_INT);
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $classes;
    }

    // Método para buscar os alunos por classe
    public function getAlunosByClasse($classe_id) {
        global $pdo;
        $query = "SELECT * FROM alunos WHERE classe_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(1, $classe_id, PDO::PARAM_INT);
        $stmt->execute();
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $alunos;
    }

    // Método para buscar o professor por ID
    public function getProfessorById($professor_id) {
        global $pdo;
        $query = "SELECT * FROM professores WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(1, $professor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para salvar a chamada
    public function salvarChamada($dados) {
        global $pdo;
        $congregacao = $dados['congregacao'];
        $classe = $dados['classe'];
        $professor = $dados['professor'];
        $data = $dados['data'];
        $alunos = $dados['alunos'];

        // Começar a transação
        $pdo->beginTransaction();

        try {
            // Salvar chamada
            $query = "INSERT INTO chamadas (congregacao_id, classe_id, professor_id, data) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(1, $congregacao, PDO::PARAM_INT);
            $stmt->bindValue(2, $classe, PDO::PARAM_INT);
            $stmt->bindValue(3, $professor, PDO::PARAM_INT);
            $stmt->bindValue(4, $data, PDO::PARAM_STR);
            $stmt->execute();
            $chamada_id = $pdo->lastInsertId(); // Obtendo o ID da última inserção

            // Salvar presença dos alunos
            foreach ($alunos as $aluno) {
                $query = "INSERT INTO presencas (chamada_id, aluno_id, presente) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->bindValue(1, $chamada_id, PDO::PARAM_INT);
                $stmt->bindValue(2, $aluno['id'], PDO::PARAM_INT);
                $stmt->bindValue(3, $aluno['presente'], PDO::PARAM_INT);
                $stmt->execute();
            }

            // Commit da transação
            $pdo->commit();
            return ['status' => 'success', 'message' => 'Chamada salva com sucesso.'];

        } catch (Exception $e) {
            // Rollback em caso de erro
            $pdo->rollBack();
            return ['status' => 'error', 'message' => 'Erro ao salvar a chamada: ' . $e->getMessage()];
        }
    }
}

?>














