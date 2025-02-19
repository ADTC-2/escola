<?php
require_once '../models/professor.php';
require_once '../config/conexao.php';

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

$professor = new Professor();

switch ($acao) {
    case 'listar':
        listarProfessores($professor);
        break;

    case 'salvar':
        salvarProfessor($professor);
        break;

    case 'editar':
        editarProfessor($professor);
        break;

    case 'excluir':
        excluirProfessor($professor);
        break;

    case 'buscar':
        buscarProfessor($professor);
        break;

    default:
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não definida']);
        break;
}

function listarProfessores($professor) {
    $professores = $professor->listar();
    if ($professores) {
        echo json_encode(['sucesso' => true, 'data' => $professores]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum professor encontrado']);
    }
}

function salvarProfessor($professor) {
    $usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : '';
    $congregacao_id = isset($_POST['congregacao_id']) ? $_POST['congregacao_id'] : '';

    if (empty($usuario_id) || empty($congregacao_id)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios não preenchidos']);
        return;
    }

    $resultado = $professor->salvar($usuario_id, $congregacao_id);
    if ($resultado) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Professor cadastrado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar professor']);
    }
}

function editarProfessor($professor) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : '';
    $congregacao_id = isset($_POST['congregacao_id']) ? $_POST['congregacao_id'] : '';

    if (empty($id) || empty($usuario_id) || empty($congregacao_id)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios não preenchidos']);
        return;
    }

    $resultado = $professor->editar($id, $usuario_id, $congregacao_id);
    if ($resultado) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Professor atualizado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao editar professor']);
    }
}

function excluirProfessor($professor) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if (empty($id)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
        return;
    }

    $resultado = $professor->excluir($id);
    if ($resultado) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Professor excluído com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir professor']);
    }
}

function buscarProfessor($professor) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if (empty($id)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
        return;
    }

    $dadosProfessor = $professor->buscar($id);
    if ($dadosProfessor) {
        echo json_encode(['sucesso' => true, 'data' => $dadosProfessor]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Professor não encontrado']);
    }
}
?>


