<?php
require_once '../config/conexao.php';

class MatriculaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Função para listar todas as matrículas
    public function listar() {
        $sql = "SELECT m.id, a.nome as aluno_nome, c.nome as classe_nome, 
        co.nome as congregacao_nome, p.usuario_id as professor_usuario_id, 
        u.nome as professor_nome, m.trimestre, m.status 
        FROM matriculas m
        JOIN alunos a ON m.aluno_id = a.id
        JOIN classes c ON m.classe_id = c.id
        JOIN congregacoes co ON m.congregacao_id = co.id
        JOIN professores p ON m.professor_id = p.id
        JOIN usuarios u ON p.usuario_id = u.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Função para cadastrar uma nova matrícula
    public function cadastrar($aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre) {
        $sql = "INSERT INTO matriculas (aluno_id, classe_id, congregacao_id, professor_id, trimestre, status, data_matricula)
                VALUES (:aluno_id, :classe_id, :congregacao_id, :professor_id, :trimestre, 'Ativo', NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':aluno_id', $aluno_id);
        $stmt->bindParam(':classe_id', $classe_id);
        $stmt->bindParam(':congregacao_id', $congregacao_id);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->bindParam(':trimestre', $trimestre);

        return $stmt->execute();
    }

    // Função para editar uma matrícula
    public function editar($id, $aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre) {
        $sql = "UPDATE matriculas SET aluno_id = :aluno_id, classe_id = :classe_id, 
                congregacao_id = :congregacao_id, professor_id = :professor_id, trimestre = :trimestre 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':aluno_id', $aluno_id);
        $stmt->bindParam(':classe_id', $classe_id);
        $stmt->bindParam(':congregacao_id', $congregacao_id);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->bindParam(':trimestre', $trimestre);

        return $stmt->execute();
    }

    // Função para excluir uma matrícula
    public function excluir($id) {
        $sql = "DELETE FROM matriculas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Função para buscar uma matrícula específica para edição
    public function buscar($id) {
        $sql = "SELECT * FROM matriculas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Função para carregar os dados dos selects
    public function listarAlunos() {
        $sql = "SELECT id, nome FROM alunos ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarClasses() {
        $sql = "SELECT id, nome FROM classes ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCongregacoes() {
        $sql = "SELECT id, nome FROM congregacoes ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProfessores() {
        $sql = "SELECT p.id, u.nome as professor_nome 
                FROM professores p 
                JOIN usuarios u ON p.usuario_id = u.id 
                ORDER BY u.nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarUsuarios() {
        $sql = "SELECT id, nome FROM usuarios ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

