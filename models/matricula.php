<?php
class Matricula {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    // Listar Matrículas
    public function listarMatriculas() {
        $query = "SELECT m.id, a.nome AS aluno_nome, c.nome AS classe_nome, co.nome AS congregacao_nome, u.nome AS professor_nome, m.data_matricula, m.status, m.trimestre
                  FROM matriculas m
                  JOIN alunos a ON m.aluno_id = a.id
                  JOIN classes c ON m.classe_id = c.id
                  JOIN congregacoes co ON m.congregacao_id = co.id
                  JOIN usuarios u ON m.professor_id = u.id AND u.perfil = 'professor'";  // Alterado para usuários e perfil 'professor'
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
        // Verificar se o professor_id existe na tabela 'usuarios' e tem o perfil 'professor'
        $queryCheckProfessor = "SELECT COUNT(*) FROM usuarios WHERE id = :professor_id AND perfil = 'professor'";
        try {
            $stmt = $this->pdo->prepare($queryCheckProfessor);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->execute();
            $professorCount = $stmt->fetchColumn();
    
            if ($professorCount == 0) {
                return ['sucesso' => false, 'mensagem' => 'Professor não encontrado ou o usuário não tem perfil de professor.'];
            }
    
            // Se o professor_id for válido, realizar o INSERT na tabela matriculas
            $query = "INSERT INTO matriculas (aluno_id, classe_id, congregacao_id, professor_id, trimestre, data_matricula, status) 
                      VALUES (:aluno_id, :classe_id, :congregacao_id, :professor_id, :trimestre, NOW(), 'Ativo')";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
            $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
            $stmt->bindParam(':congregacao_id', $congregacao_id, PDO::PARAM_INT);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->bindParam(':trimestre', $trimestre, PDO::PARAM_INT);
            $stmt->execute();
    
            return ['sucesso' => true];
    
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar matrícula: " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    
    // Excluir Matrícula
    public function excluirMatricula($matricula_id) {
        $query = "DELETE FROM matriculas WHERE id = :matricula_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            $stmt->execute();
            return ['sucesso' => true];
        } catch (PDOException $e) {
            error_log("Erro ao excluir matrícula: " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }

    // Editar Matrícula
    public function editarMatricula($matricula_id, $aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre) {
        // Verificar se o professor_id existe na tabela 'usuarios' e tem o perfil 'professor'
        $queryCheckProfessor = "SELECT COUNT(*) FROM usuarios WHERE id = :professor_id AND perfil = 'professor'";
        try {
            $stmt = $this->pdo->prepare($queryCheckProfessor);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->execute();
            $professorCount = $stmt->fetchColumn();
    
            if ($professorCount == 0) {
                return ['sucesso' => false, 'mensagem' => 'Professor não encontrado ou o usuário não tem perfil de professor.'];
            }
    
            // Verificar se a matrícula existe
            $queryCheckMatricula = "SELECT COUNT(*) FROM matriculas WHERE id = :matricula_id";
            $stmt = $this->pdo->prepare($queryCheckMatricula);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            $stmt->execute();
            $matriculaCount = $stmt->fetchColumn();
    
            if ($matriculaCount == 0) {
                return ['sucesso' => false, 'mensagem' => 'Matrícula não encontrada.'];
            }
    
            // Se o professor_id e a matrícula forem válidos, realizar o UPDATE
            $query = "UPDATE matriculas 
                      SET aluno_id = :aluno_id, classe_id = :classe_id, congregacao_id = :congregacao_id, 
                          professor_id = :professor_id, trimestre = :trimestre 
                      WHERE id = :matricula_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
            $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
            $stmt->bindParam(':congregacao_id', $congregacao_id, PDO::PARAM_INT);
            $stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->bindParam(':trimestre', $trimestre, PDO::PARAM_INT);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            $stmt->execute();
    
            return ['sucesso' => true];
    
        } catch (PDOException $e) {
            error_log("Erro ao editar matrícula: " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    

    // Obter Matrícula por ID
    public function obterMatriculaPorId($matricula_id) {
        $query = "SELECT m.id, a.nome AS aluno_nome, c.nome AS classe_nome, co.nome AS congregacao_nome, u.nome AS professor_nome, m.trimestre, m.status
                  FROM matriculas m
                  JOIN alunos a ON m.aluno_id = a.id
                  JOIN classes c ON m.classe_id = c.id
                  JOIN congregacoes co ON m.congregacao_id = co.id
                  JOIN usuarios u ON m.professor_id = u.id AND u.perfil = 'professor'
                  WHERE m.id = :matricula_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':matricula_id', $matricula_id, PDO::PARAM_INT);
            $stmt->execute();
            $matricula = $stmt->fetch(PDO::FETCH_ASSOC);  // Retorna os dados da matrícula
            
            if ($matricula) {
                return ['sucesso' => true, 'matricula' => $matricula];
            } else {
                return ['sucesso' => false, 'mensagem' => 'Matrícula não encontrada'];
            }
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao obter matrícula: ' . $e->getMessage()];
        }
    }
    
}
?>

