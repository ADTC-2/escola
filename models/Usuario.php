<?php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Função para autenticar o usuário
    public function autenticar($email, $senha) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        /*if ($user && password_verify($senha, $user['senha'])) {
            return $user;
        }*/
        if ($user && $senha === $user['senha']) {
            return $user;
        }

        return false;
    }

    // Registro de usuário
    public function registrar($nome, $email, $senha, $perfil = 'user') {
        // Verifica se o e-mail já está cadastrado
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Este e-mail já está em uso. Por favor, use outro e-mail.");
        }

        // Insere o novo usuário
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $senhaHashed = password_hash($senha, PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $senhaHashed);
        $stmt->bindParam(':perfil', $perfil);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao registrar o usuário.");
        }
    }

    // Atualizar dados do usuário
    public function atualizar($id, $nome, $email, $senha = null, $perfil = 'user') {
        $query = "UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id";
        if ($senha) {
            $senhaHashed = password_hash($senha, PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, perfil = :perfil WHERE id = :id";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':perfil', $perfil);
        if ($senha) {
            $stmt->bindParam(':senha', $senhaHashed);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar o usuário.");
        }
    }

    // Excluir usuário
    public function excluir($id) {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao excluir o usuário.");
        }
    }

    // Listar todos os usuários
    public function listar() {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar usuário por ID
    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
