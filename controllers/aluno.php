<?php
require_once "../config/conexao.php"; // Inclui a configuração de conexão

// Listar alunos com paginação
if ($_POST['acao'] == 'listar') {
    try {
        $pagina = isset($_POST['pagina']) ? (int) $_POST['pagina'] : 1;
        $itensPorPagina = 6;
        $offset = ($pagina - 1) * $itensPorPagina;

        // Atualizar a consulta SQL para adicionar o limite de página
        $sql = "SELECT * FROM alunos LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $itensPorPagina, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obter o total de alunos
        $sqlTotal = "SELECT COUNT(*) as total FROM alunos";
        $stmtTotal = $pdo->prepare($sqlTotal);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode(['sucesso' => true, 'data' => $alunos, 'total' => $total]);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar alunos: ' . $e->getMessage()]);
    }
}

// Salvar aluno
if ($_POST['acao'] == 'salvar') {
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];

    if (empty($nome) || empty($data_nascimento) || empty($telefone)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios não preenchidos']);
        exit;
    }

    $sql = "INSERT INTO alunos (nome, data_nascimento, telefone) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $data_nascimento, $telefone]);
    echo json_encode(['sucesso' => true, 'mensagem' => 'Aluno cadastrado com sucesso']);
}

// Excluir aluno
if ($_POST['acao'] == 'excluir') {
    $id = $_POST['id'];
    if (empty($id)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID do aluno não encontrado']);
        exit;
    }

    $sql = "DELETE FROM alunos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    echo json_encode(['sucesso' => true, 'mensagem' => 'Aluno excluído com sucesso']);
}

// Buscar aluno para editar
if ($_POST['acao'] == 'buscar') {
    $id = $_POST['id'];
    $sql = "SELECT * FROM alunos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        echo json_encode(['sucesso' => true, 'aluno' => $aluno]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Aluno não encontrado']);
    }
}

// Editar aluno
if ($_POST['acao'] == 'editar') {
    try {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];
        $telefone = $_POST['telefone'];

        if (empty($id) || empty($nome) || empty($data_nascimento) || empty($telefone)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios não preenchidos']);
            exit;
        }

        // Atualizar aluno
        $sql = "UPDATE alunos SET nome = ?, data_nascimento = ?, telefone = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $data_nascimento, $telefone, $id]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Aluno atualizado com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao editar aluno: ' . $e->getMessage()]);
    }
}
?>




