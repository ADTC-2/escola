<?php
require_once '../models/aluno.php';
require_once '../models/classe.php';
require_once '../config/conexao.php';

header('Content-Type: application/json');

$aluno = new Aluno($pdo);
$classe = new Classe($pdo);

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'listar':
        echo json_encode(["status" => "success", "data" => $aluno->listar()]);
        break;

    case 'buscar':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            echo json_encode($aluno->buscar($id));
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID inválido"]);
        }
        break;

    case 'salvar':
        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'data_nascimento' => $_POST['data_nascimento'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'classe_id' => $_POST['classe_id'] ?? ''
        ];

        $resultado = $aluno->salvar($dados);
        echo json_encode($resultado);
        break;

    case 'editar':
        $dados = [
            'id' => $_POST['id'] ?? '',
            'nome' => $_POST['nome'] ?? '',
            'data_nascimento' => $_POST['data_nascimento'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'classe_id' => $_POST['classe_id'] ?? ''
        ];

        $resultado = $aluno->editar($dados);
        echo json_encode($resultado);
        break;

    case 'excluir':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            echo json_encode($aluno->excluir($id));
        } else {
            echo json_encode(["status" => "error", "message" => "ID inválido"]);
        }
        break;

    case 'listar_classes':
        echo json_encode(["status" => "success", "data" => $classe->listar()]);
        break;

    default:
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Ação inválida"]);
        break;
}
?>

