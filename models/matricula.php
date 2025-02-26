<?php
require_once '../config/conexao.php';
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
                  JOIN usuarios u ON m.professor_id = u.id AND u.perfil = 'professor'";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao listar matrículas: ' . $e->getMessage()];
        }
    }

    // Listar Alunos por Classe
    public function listarAlunosPorClasse($classe_id) {
        $query = "SELECT a.id, a.nome 
                FROM alunos a
                JOIN matriculas m ON m.aluno_id = a.id
                WHERE m.classe_id = :classe_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return [
                'sucesso' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao listar alunos: ' . $e->getMessage()];
        }
    }


    // Listar Classes por Congregação
    public function listarClassesPorCongregacao($congregacao_id) {
        $query = "SELECT DISTINCT c.id, c.nome 
                  FROM classes c
                  JOIN matriculas m ON m.classe_id = c.id
                  WHERE m.congregacao_id = :congregacao_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':congregacao_id', $congregacao_id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return [
                'sucesso' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } else {
            return ['sucesso' => false, 'mensagem' => 'Erro ao buscar classes'];
        }
    }
}


