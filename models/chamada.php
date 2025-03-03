<?php
// Inclua o arquivo de configuração e conexão com o banco de dados
include_once('../config/conexao.php');

ini_set('display_errors', 1);  // Ativa a exibição de erros
ini_set('display_startup_errors', 1);  // Ativa a exibição de erros durante a inicialização
error_reporting(E_ALL);  // Exibe todos os erros


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


    
// Método para obter os alunos de uma classe
public function getAlunosByClasse($classe_id) {
    global $pdo;

    // Consulta para obter os alunos associados à classe
    $query = "SELECT a.id, a.nome
              FROM alunos a
              INNER JOIN matriculas m ON m.aluno_id = a.id
              WHERE m.classe_id = ? AND m.status = 'ativo'";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(1, $classe_id, PDO::PARAM_INT);
    $stmt->execute();

    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($alunos)) {
        $this->sendErrorResponse('Nenhum aluno encontrado para esta classe.');
    }

    return ['status' => 'success', 'data' => $alunos];
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


// Método para registrar a chamada
public function registrarChamada($data, $classeId, $professorId, $alunos, $ofertaClasse) {
    try {
        // Iniciar a transação
        $this->pdo->beginTransaction();

        // Inserir a chamada
        $sqlChamada = "INSERT INTO chamadas (data, classe_id, professor_id, oferta_classe) 
                       VALUES (:data, :classe_id, :professor_id, :oferta_classe)";
        $stmt = $this->pdo->prepare($sqlChamada);
        $stmt->execute([
            ':data' => $data,
            ':classe_id' => $classeId,
            ':professor_id' => $professorId,
            ':oferta_classe' => $ofertaClasse
        ]);
        
        // Recuperar o ID da chamada inserida
        $chamadaId = $this->pdo->lastInsertId();

        // Preparar o SQL para inserir a presença dos alunos
        $sqlPresenca = "INSERT INTO presencas (chamada_id, aluno_id, presente) 
                        VALUES (:chamada_id, :aluno_id, :presente)";
        $stmtPresenca = $this->pdo->prepare($sqlPresenca);

        // Inserir a presença para cada aluno
        foreach ($alunos as $aluno) {
            // Verificar se o aluno está presente ou ausente
            $presente = $aluno['presente'] ? 'presente' : 'ausente';
            
            // Caso o aluno tenha faltado e esteja justificado, marcar como 'justificado'
            if (isset($aluno['justificado']) && $aluno['justificado']) {
                $presente = 'justificado';
            }

            // Inserir a presença no banco de dados
            $stmtPresenca->execute([
                ':chamada_id' => $chamadaId,
                ':aluno_id' => $aluno['id'],
                ':presente' => $presente
            ]);
        }

        // Confirmar a transação
        $this->pdo->commit();

        return ['sucesso' => true];
    } catch (Exception $e) {
        // Caso ocorra erro, faz o rollback
        $this->pdo->rollBack();
        error_log("Erro ao registrar a chamada: " . $e->getMessage());  // Log do erro
        return ['sucesso' => false, 'mensagem' => $e->getMessage()];
    }
}
}

?>














