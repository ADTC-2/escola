<?php
require_once '../config/conexao.php';

class Usuario {

    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    // Listar usuários
    public function listar() {
        $query = "SELECT u.id, u.nome, u.email, u.perfil, c.nome AS congregacao_nome
                  FROM usuarios u
                  LEFT JOIN congregacoes c ON u.congregacao_id = c.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Salvar usuário
    public function salvar($nome, $email, $senha, $perfil, $congregacao_id) {
        $query = "INSERT INTO usuarios (nome, email, senha, perfil, congregacao_id) 
                  VALUES (:nome, :email, :senha, :perfil, :congregacao_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', password_hash($senha, PASSWORD_BCRYPT));
        $stmt->bindParam(':perfil', $perfil);
        $stmt->bindParam(':congregacao_id', $congregacao_id);
        return $stmt->execute();
    }

    // Editar usuário
    public function editar($id, $nome, $email, $senha, $perfil, $congregacao_id) {
        $query = "UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, congregacao_id = :congregacao_id 
                  WHERE id = :id";
        if (!empty($senha)) {
            $query = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, perfil = :perfil, congregacao_id = :congregacao_id 
                      WHERE id = :id";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':perfil', $perfil);
        $stmt->bindParam(':congregacao_id', $congregacao_id);
        if (!empty($senha)) {
            $stmt->bindParam(':senha', password_hash($senha, PASSWORD_BCRYPT));
        }
        return $stmt->execute();
    }

    // Excluir usuário
    public function excluir($id) {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Buscar usuário completo
    public function buscar($id) {
        $query = "SELECT id, nome, email, perfil, congregacao_id FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

