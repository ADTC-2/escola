<?php
// Garantir que a conexão esteja correta
require_once "../config/conexao.php"; // Inclui a configuração de conexão

// Exemplo: usando a conexão $pdo diretamente
if ($_POST['acao'] == 'listar') {
    try {
        // Buscar todos os alunos
        $sql = "SELECT * FROM alunos";
        $stmt = $pdo->prepare($sql);  // Use $pdo aqui em vez de $conexao
        $stmt->execute();
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['sucesso' => true, 'data' => $alunos]);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar alunos: ' . $e->getMessage()]);
    }
}

if ($_POST['acao'] == 'salvar') {
    // Salvar aluno
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];
    
    $sql = "INSERT INTO alunos (nome, data_nascimento, telefone) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql); // Use $pdo aqui
    $stmt->execute([$nome, $data_nascimento, $telefone]);
    echo json_encode(['sucesso' => true, 'mensagem' => 'Aluno cadastrado com sucesso']);
}

if ($_POST['acao'] == 'excluir') {
    // Excluir aluno
    $id = $_POST['id'];
    $sql = "DELETE FROM alunos WHERE id = ?";
    $stmt = $pdo->prepare($sql); // Use $pdo aqui
    $stmt->execute([$id]);
    echo json_encode(['sucesso' => true, 'mensagem' => 'Aluno excluído com sucesso']);
}

if ($_POST['acao'] == 'buscar') {
    // Buscar dados do aluno para edição
    $id = $_POST['id'];
    $sql = "SELECT * FROM alunos WHERE id = ?";
    $stmt = $pdo->prepare($sql); // Use $pdo aqui
    $stmt->execute([$id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($aluno) {
        echo json_encode(['sucesso' => true, 'aluno' => $aluno]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Aluno não encontrado']);
    }
}

if ($_POST['acao'] == 'editar') {
    try {
        $id = $_POST['id'];
        $nome = $_POST['nome'];

        // Verifica se a congregação existe
        $sqlVerifica = "SELECT id FROM congregacoes WHERE id = ?";
        $stmtVerifica = $pdo->prepare($sqlVerifica);
        $stmtVerifica->execute([$id]);
        $congregacao = $stmtVerifica->fetch();

        if (!$congregacao) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Congregação não encontrada']);
            exit;
        }

        // Atualizar congregação
        $query = "UPDATE congregacoes SET nome = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nome, $id]);

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Congregação atualizada com sucesso!'
        ]);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao editar congregação: ' . $e->getMessage()]);
    }
}

?>





