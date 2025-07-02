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
public function getAlunosByClasse($classe_id, $congregacao_id, $trimestre) {
    global $pdo;

    $query = "
        SELECT DISTINCT a.id, a.nome
        FROM alunos a
        INNER JOIN matriculas m ON m.aluno_id = a.id
        WHERE m.classe_id = :classe_id
          AND m.congregacao_id = :congregacao_id
          AND m.trimestre = :trimestre
          AND m.status = 'ativo'
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
    $stmt->bindParam(':congregacao_id', $congregacao_id, PDO::PARAM_INT);
    $stmt->bindParam(':trimestre', $trimestre, PDO::PARAM_INT);
    $stmt->execute();

    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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


// Método para registrar a chamada
public function registrarChamada($data, $trimestre, $classeId, $professorId, $alunos, $ofertaClasse = 0, $total_visitantes = 0, $total_biblias = 0, $total_revistas = 0) {
    try {
        // Validações básicas
        if (!DateTime::createFromFormat('Y-m-d', $data)) {
            throw new Exception("Formato de data inválido. Use YYYY-MM-DD");
        }


        // Iniciar transação
        $this->pdo->beginTransaction();

        // Inserir a chamada
        $sqlChamada = "INSERT INTO chamadas 
                      (data, trimestre, classe_id, professor_id, oferta_classe, 
                      total_biblias, total_revistas, total_visitantes) 
                      VALUES (:data, :trimestre, :classe_id, :professor_id, 
                      :oferta_classe, :total_biblias, :total_revistas, :total_visitantes)";
        
        $stmt = $this->pdo->prepare($sqlChamada);
        $stmt->execute([
            ':data' => $data,
            ':trimestre' => (int)$trimestre, // Garante que é inteiro
            ':classe_id' => (int)$classeId,
            ':professor_id' => (int)$professorId,
            ':oferta_classe' => number_format((float)$ofertaClasse, 2, '.', ''), // Formata para decimal(10,2)
            ':total_biblias' => (int)$total_biblias,
            ':total_revistas' => (int)$total_revistas,
            ':total_visitantes' => (int)$total_visitantes
        ]);
        
        $chamadaId = $this->pdo->lastInsertId();

        // Inserir presenças
        $sqlPresenca = "INSERT INTO presencas 
                        (chamada_id, aluno_id, presente) 
                        VALUES (:chamada_id, :aluno_id, :presente)";
        $stmtPresenca = $this->pdo->prepare($sqlPresenca);

        foreach ($alunos as $aluno) {
            if (!isset($aluno['id']) || !isset($aluno['status'])) {
                throw new Exception("Dados do aluno incompletos");
            }
            
            $presente = ($aluno['status'] === 'presente') ? 1 : 0;
            
            $stmtPresenca->execute([
                ':chamada_id' => $chamadaId,
                ':aluno_id' => (int)$aluno['id'],
                ':presente' => $presente
            ]);
        }

        $this->pdo->commit();
        return ['sucesso' => true, 'mensagem' => 'Chamada registrada com sucesso'];

    } catch (Exception $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        error_log("Erro ao registrar chamada: " . $e->getMessage());
        return ['sucesso' => false, 'mensagem' => $e->getMessage()];
    }
}
}
?>














