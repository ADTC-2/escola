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

// Método para buscar as classes de uma congregação
public function getClassesByCongregacao($congregacao_id) {
    global $pdo;
    
    // Atualizando a consulta para buscar classes da tabela matriculas, apenas as ativas
    $query = "SELECT DISTINCT c.* 
              FROM classes c
              INNER JOIN matriculas m ON m.classe_id = c.id
              WHERE m.congregacao_id = ? 
              AND m.status = 'ativo'";  // Apenas matrículas ativas
    
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(1, $congregacao_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($classes)) {
        // Se não houver classes ativas para esta congregação, enviar erro
        $this->sendErrorResponse('Nenhuma classe ativa encontrada para esta congregação.');
    }

    // Retornar classes em formato JSON
    return $classes;
}


    
// Método getAlunosByClasse
public function getAlunosByClasse($classeId, $congregacaoId) {
    // Preparar a consulta SQL
    $query = "SELECT a.id, a.nome 
              FROM alunos a
              JOIN matriculas m ON m.aluno_id = a.id
              WHERE m.classe_id = :classe_id AND m.congregacao_id = :congregacao_id AND m.status = 'ativo'";
    
    // Preparar a execução
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':classe_id', $classeId, PDO::PARAM_INT);
    $stmt->bindParam(':congregacao_id', $congregacaoId, PDO::PARAM_INT);
    $stmt->execute();

    // Recuperar os dados
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($alunos)) {
        // Caso não haja alunos, retorna um erro
        $this->sendErrorResponse('Nenhum aluno encontrado para esta classe e congregação.');
    }

    // Retornar os alunos como resposta
    return $alunos;
}

// Método sendErrorResponse (para enviar a resposta de erro)
private function sendErrorResponse($mensagem) {
    echo json_encode(['sucesso' => false, 'mensagem' => $mensagem]);
    exit();
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


    // Metodo para registrar a chamada 
    public function registrarChamada($data, $classe_id, $professor_id, $alunos, $oferta_classe) {
        try {
            // Começar a transação para garantir integridade dos dados
            $this->pdo->beginTransaction();
    
            // Inserir a chamada na tabela chamadas (ajuste conforme sua tabela)
            $queryChamada = "INSERT INTO chamadas (data, classe_id, professor_id, oferta_classe) VALUES (:data, :classe_id, :professor_id, :oferta_classe)";
            $stmtChamada = $this->pdo->prepare($queryChamada);
            $stmtChamada->bindParam(':data', $data, PDO::PARAM_STR);
            $stmtChamada->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
            $stmtChamada->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmtChamada->bindParam(':oferta_classe', $oferta_classe, PDO::PARAM_STR);
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














