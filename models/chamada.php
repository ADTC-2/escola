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
// Método para registrar a chamada
public function registrarChamada($data, $classe_id, $professor_id, $alunos, $oferta_classe) {
    try {
        // Começar a transação
        $this->pdo->beginTransaction();

        // Inserir a chamada (registro principal)
        $queryChamada = "INSERT INTO chamadas (data, classe_id, professor_id, oferta_classe) 
                         VALUES (:data, :classe_id, :professor_id, :oferta_classe)";
        $stmtChamada = $this->pdo->prepare($queryChamada);
        $stmtChamada->bindParam(':data', $data, PDO::PARAM_STR);
        $stmtChamada->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        $stmtChamada->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
        $stmtChamada->bindParam(':oferta_classe', $oferta_classe, PDO::PARAM_STR);

        // Verificar o que será enviado para a query
        var_dump($data, $classe_id, $professor_id, $oferta_classe); // Depuração

        $stmtChamada->execute();

        // Obter o ID da chamada recém-criada
        $chamada_id = $this->pdo->lastInsertId();

        // Registrar as presenças e faltas dos alunos na tabela 'presencas'
        foreach ($alunos as $aluno) {
            // Verificar os dados de cada aluno antes de inserir
            var_dump($aluno); // Depuração

            $presenca = $aluno['presente'] ? 'presente' : 'ausente'; // Se o aluno está presente ou ausente
            $falta = $aluno['falta'] ? 'justificado' : $presenca; // Se a falta está marcada, marca como "justificado", senão mantém a presença

            // Inserção de cada aluno na chamada
            $queryPresenca = "INSERT INTO presencas (chamada_id, aluno_id, presente) 
                              VALUES (:chamada_id, :aluno_id, :presente)";
            $stmtPresenca = $this->pdo->prepare($queryPresenca);
            $stmtPresenca->bindParam(':chamada_id', $chamada_id, PDO::PARAM_INT);  // Referência à chamada
            $stmtPresenca->bindParam(':aluno_id', $aluno['id'], PDO::PARAM_INT);  // Referência ao aluno
            $stmtPresenca->bindParam(':presente', $falta, PDO::PARAM_STR);  // Valor da presença (presente, ausente, justificado)
            
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


}

?>














