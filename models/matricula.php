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

    public function criarMatricula($data) {
        try {
            // Validar se todos os dados obrigatórios foram recebidos
            if (empty($data['aluno_id']) || empty($data['classe_id']) || empty($data['congregacao_id']) || empty($data['status']) || empty($data['professor_id']) || empty($data['trimestre'])) {
                throw new Exception("Todos os campos obrigatórios devem ser preenchidos.");
            }
    
            // Verificar se o aluno já está matriculado na mesma classe e congregação
            if ($this->verificarMatriculaExistente($data['aluno_id'], $data['classe_id'], $data['congregacao_id'])) {
                throw new Exception("Este aluno já está matriculado nesta classe e congregação.");
            }
    
            // Definir a data de matrícula como a data atual, se não for fornecida
            $data_matricula = !empty($data['data_matricula']) ? $data['data_matricula'] : date('Y-m-d');
    
            // Verificar se a data de matrícula é válida
            if (!strtotime($data_matricula)) {
                throw new Exception("Data de matrícula inválida.");
            }
    
            // Inserir no banco de dados
            $sql = "INSERT INTO matriculas (aluno_id, classe_id, congregacao_id, usuario_id, data_matricula, status, trimestre)
                    VALUES (:aluno_id, :classe_id, :congregacao_id, :usuario_id, :data_matricula, :status, :trimestre)";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':aluno_id' => $data['aluno_id'],
                ':classe_id' => $data['classe_id'],
                ':congregacao_id' => $data['congregacao_id'],
                ':usuario_id' => $data['professor_id'], // Correção aqui
                ':data_matricula' => $data_matricula,
                ':status' => $data['status'],
                ':trimestre' => $data['trimestre']
            ]);
    
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar matrícula: " . $e->getMessage());
            throw new Exception("Erro ao criar matrícula: " . $e->getMessage());
        }
    }
    
    // Atualizar uma matrícula existente
// No método atualizarMatricula do Model
public function atualizarMatricula($id, $data) {
    try {
        $sql = "UPDATE matriculas SET 
                aluno_id = :aluno_id, 
                classe_id = :classe_id, 
                congregacao_id = :congregacao_id, 
                usuario_id = :usuario_id, 
                trimestre = :trimestre, 
                status = :status 
                WHERE id = :id";
                
        $stmt = $this->pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':id' => $id,
            ':aluno_id' => $data['aluno_id'],
            ':classe_id' => $data['classe_id'],
            ':congregacao_id' => $data['congregacao_id'],
            ':usuario_id' => $data['professor_id'],
            ':trimestre' => $data['trimestre'],
            ':status' => $data['status']
        ]);
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Erro ao atualizar matrícula (Model): " . $e->getMessage());
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

    // Verificar se a matrícula já existe (evitar duplicação)
    public function verificarMatriculaExistente($aluno_id, $classe_id, $congregacao_id) {
        $sql = "SELECT COUNT(*) FROM matriculas WHERE aluno_id = :aluno_id AND classe_id = :classe_id AND congregacao_id = :congregacao_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':aluno_id' => $aluno_id, ':classe_id' => $classe_id, ':congregacao_id' => $congregacao_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Verificar se a matrícula existe para exclusão
    public function verificarMatriculaExistenteParaExclusao($id) {
        $sql = "SELECT COUNT(*) FROM matriculas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    // Carregar dados para selects
    public function carregarSelects() {
        $sql_alunos = "SELECT id, nome FROM alunos";
        $sql_classes = "SELECT id, nome FROM classes";
        $sql_congregacoes = "SELECT id, nome FROM congregacoes";
        $sql_usuarios = "SELECT id, nome FROM usuarios";

        $stmt_alunos = $this->pdo->prepare($sql_alunos);
        $stmt_classes = $this->pdo->prepare($sql_classes);
        $stmt_congregacoes = $this->pdo->prepare($sql_congregacoes);
        $stmt_usuarios = $this->pdo->prepare($sql_usuarios);

        $stmt_alunos->execute();
        $stmt_classes->execute();
        $stmt_congregacoes->execute();
        $stmt_usuarios->execute();

        return [
            'alunos' => $stmt_alunos->fetchAll(PDO::FETCH_ASSOC),
            'classes' => $stmt_classes->fetchAll(PDO::FETCH_ASSOC),
            'congregacoes' => $stmt_congregacoes->fetchAll(PDO::FETCH_ASSOC),
            'usuarios' => $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC),
        ];
    }
    public function listarMatriculasPorTrimestre($trimestre_atual) {
        $stmt = $this->pdo->prepare("SELECT * FROM matriculas WHERE trimestre = :trimestre_atual");
        $stmt->bindParam(':trimestre_atual', $trimestre_atual);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Migrar as matrículas para o próximo trimestre
    public function migrarMatriculaParaNovoTrimestre($matricula, $trimestre_novo) {
        $sql = "UPDATE matriculas SET trimestre = :trimestre_novo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':trimestre_novo' => $trimestre_novo, ':id' => $matricula['id']]);
    }

    public function buscarMatriculaPorId($id) {
        try {
            $sql = "SELECT m.id, m.aluno_id, m.classe_id, m.congregacao_id, m.usuario_id, 
                           m.trimestre, m.status, m.data_matricula 
                    FROM matriculas m
                    WHERE m.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar matrícula.");
        }
    }

    public function migrarMatriculasParaNovoTrimestre($trimestre_atual, $trimestre_novo, $congregacao_id, $manter_status = true) {
        try {
            // Busca todas as matrículas do trimestre atual e congregação
            $sql = "SELECT * FROM matriculas 
                    WHERE trimestre = :trimestre_atual 
                    AND congregacao_id = :congregacao_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':trimestre_atual' => $trimestre_atual,
                ':congregacao_id' => $congregacao_id
            ]);
            
            $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($matriculas)) {
                throw new Exception("Nenhuma matrícula encontrada para o trimestre e congregação selecionados.");
            }
            
            // Inicia transação
            $this->pdo->beginTransaction();
            
            foreach ($matriculas as $matricula) {
                $novo_status = $manter_status ? $matricula['status'] : 'ativo';
                
                // Verifica se já existe matrícula para o mesmo aluno, classe e trimestre
                if (!$this->verificarMatriculaExistenteParaTrimestre(
                    $matricula['aluno_id'],
                    $matricula['classe_id'],
                    $matricula['congregacao_id'],
                    $trimestre_novo
                )) {
                    $sql_insert = "INSERT INTO matriculas 
                                  (aluno_id, classe_id, congregacao_id, usuario_id, data_matricula, status, trimestre)
                                  VALUES (:aluno_id, :classe_id, :congregacao_id, :usuario_id, :data_matricula, :status, :trimestre)";
                    
                    $stmt_insert = $this->pdo->prepare($sql_insert);
                    $stmt_insert->execute([
                        ':aluno_id' => $matricula['aluno_id'],
                        ':classe_id' => $matricula['classe_id'],
                        ':congregacao_id' => $matricula['congregacao_id'],
                        ':usuario_id' => $matricula['usuario_id'],
                        ':data_matricula' => date('Y-m-d'),
                        ':status' => $novo_status,
                        ':trimestre' => $trimestre_novo
                    ]);
                }
            }
            
            $this->pdo->commit();
            return ['sucesso' => true, 'mensagem' => 'Matrículas migradas com sucesso para o novo trimestre.'];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erro ao migrar matrículas (Model): " . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }
    
    private function verificarMatriculaExistenteParaTrimestre($aluno_id, $classe_id, $congregacao_id, $trimestre) {
        $sql = "SELECT COUNT(*) as total FROM matriculas 
                WHERE aluno_id = :aluno_id 
                AND classe_id = :classe_id
                AND congregacao_id = :congregacao_id
                AND trimestre = :trimestre";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':aluno_id' => $aluno_id,
            ':classe_id' => $classe_id,
            ':congregacao_id' => $congregacao_id,
            ':trimestre' => $trimestre
        ]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }

    public function verificarMatriculaExistenteNoMesmoTrimestre($aluno_id, $trimestre) {
    $sql = "SELECT COUNT(*) FROM matriculas 
            WHERE aluno_id = :aluno_id 
            AND trimestre = :trimestre
            AND status != 'inativo'";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
    $stmt->bindParam(':trimestre', $trimestre, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}

}
?>





