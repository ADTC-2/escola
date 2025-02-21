<?php
require_once '../config/conexao.php';

class Classe {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarClasses() {
        $stmt = $this->pdo->prepare("SELECT * FROM classes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar() {
        $stmt = $this->pdo->prepare("SELECT * FROM classes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarPorId($id) {
        try {
            $query = "SELECT * FROM classes WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return ['erro' => 'Erro ao buscar classe: ' . $e->getMessage()];
        }
    }

    public function salvar($nome, $congregacao_id) {
        try {
            $query = "INSERT INTO classes (nome, congregacao_id) VALUES (:nome, :congregacao_id)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':nome' => $nome, ':congregacao_id' => $congregacao_id]);

            return ['sucesso' => true, 'mensagem' => 'Classe cadastrada com sucesso!'];
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao salvar classe: ' . $e->getMessage()];
        }
    }

    public function editar($id, $nome, $congregacao_id) {
        try {
            if (!$this->buscarPorId($id)) {
                return ['sucesso' => false, 'mensagem' => 'Classe não encontrada'];
            }

            $query = "UPDATE classes SET nome = :nome, congregacao_id = :congregacao_id WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id, ':nome' => $nome, ':congregacao_id' => $congregacao_id]);

            return ['sucesso' => true, 'mensagem' => 'Classe atualizada com sucesso!'];
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao editar classe: ' . $e->getMessage()];
        }
    }

    public function excluir($id) {
        try {
            if (!$this->buscarPorId($id)) {
                return ['sucesso' => false, 'mensagem' => 'Classe não encontrada'];
            }

            // Verifica se há alunos vinculados
            $queryCheck = "SELECT COUNT(*) FROM alunos WHERE id = ?";
            $stmtCheck = $this->pdo->prepare($queryCheck);
            $stmtCheck->execute([$id]);

            if ($stmtCheck->fetchColumn() > 0) {
                return ['sucesso' => false, 'mensagem' => 'Não é possível excluir a classe. Existem alunos vinculados.'];
            }

            $query = "DELETE FROM classes WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);

            return ['sucesso' => true, 'mensagem' => 'Classe excluída com sucesso!'];
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao excluir classe: ' . $e->getMessage()];
        }
    }
}



