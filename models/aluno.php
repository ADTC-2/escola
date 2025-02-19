<?php
require_once "../config/conexao.php";

class Aluno {
    private $db;

    public function __construct() {
        global $pdo;  // Assumindo que você tenha um objeto de conexão global
        $this->db = $pdo;
    }

    public function listarAlunos() {
        $stmt = $this->db->prepare("SELECT * FROM alunos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar() {
        $stmt = $this->db->prepare("SELECT * FROM alunos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM alunos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($dados) {
        $stmt = $this->db->prepare("INSERT INTO alunos (nome, data_nascimento, telefone) VALUES (?, ?, ?)");
        return $stmt->execute([$dados["nome"], $dados["data_nascimento"], $dados["telefone"]]);
    }

    public function editar($dados) {
        $stmt = $this->db->prepare("UPDATE alunos SET nome = ?, data_nascimento = ?, telefone = ? WHERE id = ?");
        return $stmt->execute([$dados["nome"], $dados["data_nascimento"], $dados["telefone"], $dados["id"]]);
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("DELETE FROM alunos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>

