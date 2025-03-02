<?php
require_once '../config/conexao.php';

class Matricula {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Listar as matrículas
    public function listarMatriculas() {
        try {
            $sql = "SELECT m.id, a.nome AS aluno, c.nome AS classe, cg.nome AS congregacao, u.nome AS usuario, m.data_matricula, m.status, m.trimestre
                    FROM matriculas m
                    JOIN alunos a ON m.aluno_id = a.id
                    JOIN classes c ON m.classe_id = c.id
                    JOIN congregacoes cg ON m.congregacao_id = cg.id
                    LEFT JOIN usuarios u ON m.usuario_id = u.id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar matrículas.");
        }
    }

    // Criar uma nova matrícula
    public function criarMatricula($data) {
        try {
            // Verifica se as chaves existem no array e define valores padrão (null)
            $aluno_id = isset($data['aluno_id']) ? $data['aluno_id'] : null;
            $classe_id = isset($data['classe_id']) ? $data['classe_id'] : null;
            $congregacao_id = isset($data['congregacao_id']) ? $data['congregacao_id'] : null;
            $professor_id = isset($data['professor_id']) ? $data['professor_id'] : null;
            $data_matricula = isset($data['data_matricula']) ? $data['data_matricula'] : date('Y-m-d');  // Corrigido: Se não houver data, usa a data atual
            $status = isset($data['status']) ? $data['status'] : null;  // Status aqui
            $trimestre = isset($data['trimestre']) ? $data['trimestre'] : null;
            
            // Valida se os campos essenciais estão preenchidos
            if ($aluno_id && $classe_id && $congregacao_id && $status) {
                $sql = "INSERT INTO matriculas (aluno_id, classe_id, congregacao_id, usuario_id, data_matricula, status, trimestre)
                        VALUES (:aluno_id, :classe_id, :congregacao_id, :professor_id, :data_matricula, :status, :trimestre)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':aluno_id' => $aluno_id,
                    ':classe_id' => $classe_id,
                    ':congregacao_id' => $congregacao_id,
                    ':professor_id' => $professor_id,
                    ':data_matricula' => $data_matricula,
                    ':status' => $status,  // Inclui status aqui
                    ':trimestre' => $trimestre
                ]);
            } else {
                throw new Exception("Dados incompletos para criar a matrícula.");
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao criar matrícula: " . $e->getMessage());
        }
    }

    // Atualizar uma matrícula existente
    public function atualizarMatricula($id, $data) {
        try {
            // Verifica se as chaves existem no array e define valores padrão (null)
            $aluno_id = isset($data['aluno_id']) ? $data['aluno_id'] : null;
            $classe_id = isset($data['classe_id']) ? $data['classe_id'] : null;
            $congregacao_id = isset($data['congregacao_id']) ? $data['congregacao_id'] : null;
            $usuario_id = isset($data['usuario_id']) ? $data['usuario_id'] : null;
            $status = isset($data['status']) ? $data['status'] : null;
            $trimestre = isset($data['trimestre']) ? $data['trimestre'] : null;

            if ($id) {
                $sql = "UPDATE matriculas SET aluno_id = :aluno_id, classe_id = :classe_id, congregacao_id = :congregacao_id, 
                        usuario_id = :usuario_id, status = :status, trimestre = :trimestre 
                        WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':id' => $id,
                    ':aluno_id' => $aluno_id,
                    ':classe_id' => $classe_id,
                    ':congregacao_id' => $congregacao_id,
                    ':usuario_id' => $usuario_id,
                    ':status' => $status,
                    ':trimestre' => $trimestre
                ]);
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar matrícula.");
        }
    }

    // Excluir uma matrícula
    public function excluirMatricula($id) {
        try {
            $sql = "DELETE FROM matriculas WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir matrícula.");
        }
    }

    public function carregarSelects() {
        // Implement the logic to load the select options
        // Example:
        $alunos = $this->pdo->query("SELECT id, nome FROM alunos")->fetchAll();
        $classes = $this->pdo->query("SELECT id, nome FROM classes")->fetchAll();
        $congregacoes = $this->pdo->query("SELECT id, nome FROM congregacoes")->fetchAll();
        $usuarios = $this->pdo->query("SELECT id, nome FROM usuarios")->fetchAll();

        return [
            'alunos' => $alunos,
            'classes' => $classes,
            'congregacoes' => $congregacoes,
            'usuarios' => $usuarios
        ];
    }
}



