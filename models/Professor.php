<?php
class Professor {
    private $conn;
    private $table = "professores";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function criar($usuario_id, $congregacao_id) {
        $sql = "INSERT INTO " . $this->table . " (usuario_id, congregacao_id) VALUES (:usuario_id, :congregacao_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":congregacao_id", $congregacao_id);
        return $stmt->execute();
    }

    public function listar() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $usuario_id, $congregacao_id) {
        $sql = "UPDATE " . $this->table . " SET usuario_id = :usuario_id, congregacao_id = :congregacao_id WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":congregacao_id", $congregacao_id);
        return $stmt->execute();
    }

    public function deletar($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
