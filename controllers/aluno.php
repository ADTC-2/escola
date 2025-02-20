<?php
require_once '../models/aluno.php';
require_once '../config/conexao.php';

header('Content-Type: application/json'); // Garante que a resposta seja JSON

$aluno = new Aluno($pdo);

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'listar':
        $dados = $aluno->listar();

        // Garante que retorna um JSON válido com a chave "data"
        echo json_encode(["data" => $dados]);
        break;

    case 'buscar':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            echo json_encode($aluno->buscar($id));
        } else {
            echo json_encode(["status" => "error", "message" => "ID inválido"]);
        }
        break;

    case 'salvar':
        $nome = htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8');
        $data_nascimento = htmlspecialchars($_POST['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8');
        $telefone = htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($nome && $data_nascimento && $telefone) {
            $resultado = $aluno->salvar($nome, $data_nascimento, $telefone);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Dados inválidos"]);
        }
        break;

    case 'editar':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8');
        $data_nascimento = htmlspecialchars($_POST['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8');
        $telefone = htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES, 'UTF-8');

        if ($id && $nome && $data_nascimento && $telefone) {
            $resultado = $aluno->editar($id, $nome, $data_nascimento, $telefone);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Dados inválidos"]);
        }
        break;

    case 'excluir':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            $resultado = $aluno->excluir($id);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "ID inválido"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Requisição inválida"]);
        break;
}
?>




