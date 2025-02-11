<?php
class Classe {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Função para listar todas as classes
    public function listarClasses() {
        $stmt = $this->pdo->prepare("SELECT * FROM classes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Função para registrar uma nova classe
    public function registrarClasse($nome, $professor_id) {
        $stmt = $this->pdo->prepare("INSERT INTO classes (nome, professor_id) VALUES (:nome, :professor_id)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->execute();
    }

    // Função para editar uma classe
    public function editarClasse($id, $nome, $professor_id) {
        $stmt = $this->pdo->prepare("UPDATE classes SET nome = :nome, professor_id = :professor_id WHERE id = :id");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Função para excluir uma classe
    public function excluirClasse($id) {
        $stmt = $this->pdo->prepare("DELETE FROM classes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Função para pegar as informações de uma classe específica
    public function getClasse($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM classes WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

