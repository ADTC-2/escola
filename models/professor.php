<?php
require_once '../config/conexao.php';

class Professor {
    private $db;

    public function __construct() {
        global $pdo;  // Assumindo que você tenha um objeto de conexão global
        $this->db = $pdo;
    }

    // Listar todos os professores
    public function listar() {
        $sql = "SELECT u.id, u.nome, u.email, u.perfil 
                FROM usuarios u 
                WHERE u.perfil = 'professor'"; // Filtra apenas usuários com perfil de professor
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar professor por ID
    public function buscar($id) {
        $query = "SELECT p.id, p.usuario_id, p.congregacao_id
                  FROM professores p
                  WHERE p.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Salvar novo professor
    public function salvar($usuario_id, $congregacao_id) {
        $query = "INSERT INTO professores (usuario_id, congregacao_id) 
                  VALUES (:usuario_id, :congregacao_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':congregacao_id', $congregacao_id);
        return $stmt->execute();
    }

    // Editar professor
    public function editar($id, $usuario_id, $congregacao_id) {
        $query = "UPDATE professores 
                  SET usuario_id = :usuario_id, congregacao_id = :congregacao_id
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':congregacao_id', $congregacao_id);
        return $stmt->execute();
    }

    // Excluir professor
    public function excluir($id) {
        $query = "DELETE FROM professores WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>

