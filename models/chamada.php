<?php
require_once '../config/conexao.php';

class Chamada
{
    // Função para obter todas as congregações
    public static function getCongregacoes()
    {
        global $pdo;
        $sql = "SELECT * FROM congregacoes";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getClassesByCongregacao($congregacaoId)
    {
        global $pdo;
    
        // SQL para obter as classes de uma congregação específica
        $sql = "SELECT * FROM classes WHERE congregacao_id = :congregacao_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':congregacao_id', $congregacaoId, PDO::PARAM_INT);
        
        // Executa a consulta
        $stmt->execute();
    
        // Recupera as classes
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Verifica se foram encontradas classes
        if (count($classes) > 0) {
            return $classes;  // Retorna as classes
        } else {
            return ["status" => "error", "message" => "Nenhuma classe encontrada para essa congregação."];
        }
    }
    
    
    
    



    // Função para obter um professor
    public static function getProfessor($professorId)
    {
        global $pdo;
        $sql = "SELECT * FROM professores WHERE id = :professor_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':professor_id', $professorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Função para obter os alunos de uma classe
    public static function getAlunosByClasse($classeId)
    {
        global $pdo;
        $sql = "SELECT a.id, a.nome
                FROM alunos a
                WHERE a.classe_id = :classe_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':classe_id', $classeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Função para salvar a chamada (presença)
    public static function registrarChamada($classeId, $professorId, $dataChamada, $alunos)
    {
        global $pdo;
        $pdo->beginTransaction();
        
        try {
            $sql = "INSERT INTO chamadas (data, classe_id, professor_id)
                    VALUES (:data, :classe_id, :professor_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':data', $dataChamada, PDO::PARAM_STR);
            $stmt->bindParam(':classe_id', $classeId, PDO::PARAM_INT);
            $stmt->bindParam(':professor_id', $professorId, PDO::PARAM_INT);
            $stmt->execute();

            $chamadaId = $pdo->lastInsertId();

            foreach ($alunos as $aluno) {
                $sql = "INSERT INTO chamada_alunos (chamada_id, aluno_id, presente)
                        VALUES (:chamada_id, :aluno_id, :presente)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':chamada_id', $chamadaId, PDO::PARAM_INT);
                $stmt->bindParam(':aluno_id', $aluno['id'], PDO::PARAM_INT);
                $stmt->bindParam(':presente', $aluno['presente'], PDO::PARAM_INT);
                $stmt->execute();
            }

            $pdo->commit();
            return ['status' => 'success', 'message' => 'Chamada registrada com sucesso!'];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['status' => 'error', 'message' => 'Erro ao registrar a chamada: ' . $e->getMessage()];
        }
    }
}
?>











