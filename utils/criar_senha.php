<?php
// Função para gerar a senha
function gerarSenha($tamanho = 12) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_-+=<>?';
    $senha = '123456';
    $caracteresLength = strlen($caracteres);

    for ($i = 0; $i < $tamanho; $i++) {
        $senha .= $caracteres[rand(0, $caracteresLength - 1)];
    }

    return $senha;
}

// Função para gerar, criptografar e armazenar a senha no banco de dados
function gerarEArmazenarSenha($email, $tamanhoSenha = 16) {
    // Gerar a senha aleatória
    $senhaGerada = gerarSenha($tamanhoSenha);
    
    // Criptografar a senha com bcrypt
    $senhaHash = password_hash($senhaGerada, PASSWORD_DEFAULT);

    // Conectar ao banco de dados (faça a sua própria conexão aqui)
    require_once "../config/conexao.php";  // Ajuste o caminho do arquivo de conexão se necessário

    // Atualiza a senha no banco de dados
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmt->execute([$senhaHash, $email]);
        
        echo "Senha gerada e armazenada com sucesso!";
        // NÃO exiba a senha gerada em produção
        // echo "<br>Senha gerada: " . $senhaGerada;  // Para fins de depuração, remova essa linha em produção
    } catch (PDOException $e) {
        echo "Erro ao atualizar a senha: " . $e->getMessage();
    }
}

// Exemplo de uso da função
$senha = "admin@gmail.com";  // Substitua pelo email do usuário
gerarEArmazenarSenha($senha);
?>

