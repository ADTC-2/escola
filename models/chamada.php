<?php
// Inclua o arquivo de configuração e conexão com o banco de dados
include_once('../config/conexao.php');

class Chamada {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }



    // Método para buscar todas as congregações
    public function getCongregacoes() {
        global $pdo;
        $query = "SELECT * FROM congregacoes";
        $stmt = $pdo->query($query);
        $congregacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $congregacoes;
    }

    public function getClassesByCongregacao($congregacao_id) {
        global $pdo;
    
        // Atualizando a consulta para buscar classes da tabela matriculas
        $query = "SELECT DISTINCT c.* 
                  FROM classes c
                  INNER JOIN matriculas m ON m.classe_id = c.id
                  WHERE m.congregacao_id = ?";
        
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
    public function getProfessor($professor_id) {
        $query = "SELECT id, nome FROM usuarios WHERE id = :professor_id AND perfil = 'professor'";
    
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->execute();
            $professor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($professor) {
                return ['sucesso' => true, 'professor' => $professor];
            } else {
                return ['sucesso' => false, 'mensagem' => 'Professor não encontrado.'];
            }
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar professor: ' . $e->getMessage()];
        }
    }
    public function registrarChamada($data, $classe_id, $professor_id, $alunos) {
        try {
            // Começar a transação para garantir integridade dos dados
            $this->pdo->beginTransaction();
    
            // Inserir a chamada na tabela chamadas (ajuste conforme sua tabela)
            $queryChamada = "INSERT INTO chamadas (data, classe_id, professor_id) VALUES (:data, :classe_id, :professor_id)";
            $stmtChamada = $this->pdo->prepare($queryChamada);
            $stmtChamada->bindParam(':data', $data, PDO::PARAM_STR);
            $stmtChamada->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
            $stmtChamada->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmtChamada->execute();
    
            // Obter o ID da chamada inserida
            $chamada_id = $this->pdo->lastInsertId();
    
            // Registrar as presenças
            foreach ($alunos as $aluno) {
                $queryPresenca = "INSERT INTO presencas (chamada_id, aluno_id, presente) VALUES (:chamada_id, :aluno_id, :presente)";
                $stmtPresenca = $this->pdo->prepare($queryPresenca);
                $stmtPresenca->bindParam(':chamada_id', $chamada_id, PDO::PARAM_INT);
                $stmtPresenca->bindParam(':aluno_id', $aluno['aluno_id'], PDO::PARAM_INT);
                $stmtPresenca->bindParam(':presente', $aluno['presente'], PDO::PARAM_BOOL);
                $stmtPresenca->execute();
            }
    
            // Commit da transação
            $this->pdo->commit();
    
            return ['sucesso' => true, 'mensagem' => 'Chamada registrada com sucesso!'];
    
        } catch (PDOException $e) {
            // Reverter transação em caso de erro
            $this->pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Erro ao registrar chamada: ' . $e->getMessage()];
        }
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
                $query = "INSERT INTO chamada_alunos (chamada_id, aluno_id, presente) VALUES (?, ?, ?)";
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














