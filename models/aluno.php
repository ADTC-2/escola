<?php
require_once '../config/conexao.php';

class Aluno {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function listar() {
        try {
            $stmt = $this->db->query("SELECT * FROM alunos ORDER BY nome");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function buscar($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM alunos WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
            return $aluno ?: ["status" => "error", "message" => "Aluno não encontrado"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function salvar($nome, $data_nascimento, $telefone) {
        try {
            $stmt = $this->db->prepare("INSERT INTO alunos (nome, data_nascimento, telefone) VALUES (:nome, :data_nascimento, :telefone)");
            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":data_nascimento", $data_nascimento);
            $stmt->bindValue(":telefone", $telefone);
            $stmt->execute();
            return ["status" => "success", "message" => "Aluno cadastrado com sucesso"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function editar($id, $nome, $data_nascimento, $telefone) {
        try {
            $stmt = $this->db->prepare("UPDATE alunos SET nome = :nome, data_nascimento = :data_nascimento, telefone = :telefone WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":nome", $nome);
            $stmt->bindValue(":data_nascimento", $data_nascimento);
            $stmt->bindValue(":telefone", $telefone);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["status" => "success", "message" => "Aluno atualizado com sucesso"];
            } else {
                return ["status" => "error", "message" => "Nenhuma alteração realizada"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function excluir($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM alunos WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["status" => "success", "message" => "Aluno excluído com sucesso"];
            } else {
                return ["status" => "error", "message" => "Aluno não encontrado"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>



