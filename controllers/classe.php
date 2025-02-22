<?php
require_once '../config/conexao.php';
require_once '../models/classe.php';

header('Content-Type: application/json');

$classeModel = new Classe($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    switch ($acao) {
        case 'listar':
            echo json_encode(['sucesso' => true, 'data' => $classeModel->listar()]);
            break;

        case 'salvar':
            if (!empty($_POST['nome']) && !empty($_POST['congregacao_id'])) {
                $nome = trim($_POST['nome']);
                $congregacao_id = (int) $_POST['congregacao_id'];
                echo json_encode($classeModel->salvar($nome, $congregacao_id));
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            }
            break;

        case 'editar':
            if (!empty($_POST['id']) && !empty($_POST['nome']) && !empty($_POST['congregacao_id'])) {
                $id = (int) $_POST['id'];
                $nome = trim($_POST['nome']);
                $congregacao_id = (int) $_POST['congregacao_id'];
                echo json_encode($classeModel->editar($id, $nome, $congregacao_id));
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            }
            break;

        case 'excluir':
            if (!empty($_POST['id'])) {
                $id = (int) $_POST['id'];
                echo json_encode($classeModel->excluir($id));
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
            }
            break;

        case 'buscar':
            if (!empty($_POST['id'])) {
                $id = (int) $_POST['id'];
                $classe = $classeModel->buscarPorId($id);
                if ($classe) {
                    echo json_encode(['sucesso' => true, 'data' => $classe]);
                } else {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Classe não encontrada']);
                }
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
            }
            break;

        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
            break;
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não especificada']);
}

