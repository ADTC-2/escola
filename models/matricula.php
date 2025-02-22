<?php
class Matricula {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    // Listar Matrículas
    public function listarMatriculas() {
        $query = "SELECT m.id, a.nome AS aluno_nome, c.nome AS classe_nome, co.nome AS congregacao_nome, p.nome AS professor_nome, m.data_matricula, m.status, m.trimestre
                  FROM matriculas m
                  JOIN alunos a ON m.aluno_id = a.id
                  JOIN classes c ON m.classe_id = c.id
                  JOIN congregacoes co ON m.congregacao_id = co.id
                  JOIN professores p ON m.professor_id = p.id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao listar matrículas: ' . $e->getMessage()];
        }
    }

    // Cadastrar Matrícula
    public function cadastrarMatricula($aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre) {
        $query = "INSERT INTO matriculas (aluno_id, classe_id, congregacao_id, professor_id, trimestre, data_matricula, status) 
                  VALUES (:aluno_id, :classe_id, :congregacao_id, :professor_id, :trimestre, NOW(), 'Ativo')";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
            $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
            $stmt->bindParam(':congregacao_id', $congregacao_id, PDO::PARAM_INT);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->bindParam(':trimestre', $trimestre, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Excluir Matrícula
    public function excluirMatricula($matricula_id) {
        $query = "DELETE FROM matriculas WHERE id = :matricula_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Editar Matrícula
    public function editarMatricula($matricula_id, $aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre) {
        $query = "UPDATE matriculas SET aluno_id = :aluno_id, classe_id = :classe_id, congregacao_id = :congregacao_id, professor_id = :professor_id, trimestre = :trimestre WHERE id = :matricula_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
            $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
            $stmt->bindParam(':congregacao_id', $congregacao_id, PDO::PARAM_INT);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->bindParam(':trimestre', $trimestre, PDO::PARAM_INT);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obter Matrícula por ID
    public function obterMatriculaPorId($matricula_id) {
        $query = "SELECT m.id, a.nome AS aluno_nome, c.nome AS classe_nome, co.nome AS congregacao_nome, p.nome AS professor_nome, m.trimestre, m.status
                  FROM matriculas m
                  JOIN alunos a ON m.aluno_id = a.id
                  JOIN classes c ON m.classe_id = c.id
                  JOIN congregacoes co ON m.congregacao_id = co.id
                  JOIN professores p ON m.professor_id = p.id
                  WHERE m.id = :matricula_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);  // Retorna os dados da matrícula
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao obter matrícula: ' . $e->getMessage()];
        }
    }
}
?>

