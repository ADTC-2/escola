<?php
require_once '../config/conexao.php';

class Aluno {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Cadastrar aluno
    public function cadastrar($nome, $data_nascimento, $telefone, $email, $endereco, $congregacao_id = null) {
        try {
            $sql = "INSERT INTO alunos (nome, data_nascimento, telefone, email, endereco, congregacao_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $data_nascimento, $telefone, $email, $endereco, $congregacao_id]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Listar alunos com o nome da congregação
    public function listar() {
        try {
            $sql = "SELECT a.*, c.nome AS congregacao FROM alunos a 
                    LEFT JOIN congregacoes c ON a.congregacao_id = c.id 
                    ORDER BY a.nome ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    // Buscar aluno por ID
    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM alunos WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    // Atualizar aluno
    public function atualizar($id, $nome, $data_nascimento, $telefone, $email, $endereco, $congregacao_id = null) {
        try {
            $sql = "UPDATE alunos 
                    SET nome=?, data_nascimento=?, telefone=?, email=?, endereco=?, congregacao_id=? 
                    WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $data_nascimento, $telefone, $email, $endereco, $congregacao_id, $id]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Excluir aluno
    public function excluir($id) {
        try {
            $sql = "DELETE FROM alunos WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>



