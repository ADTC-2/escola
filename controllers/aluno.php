<?php
require_once '../models/aluno.php';
require_once '../models/classe.php';  // Incluindo o modelo da classe
require_once '../config/conexao.php';

header('Content-Type: application/json');

$aluno = new Aluno($pdo);
$classe = new Classe($pdo);  // Criando uma instância da classe

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'listar':
        $dados = $aluno->listar();
        if (isset($dados['status']) && $dados['status'] == 'error') {
            http_response_code(400);
            echo json_encode($dados);
        } else {
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $dados]);
        }
        break;

    case 'buscar':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            echo json_encode($aluno->buscar($id));
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "ID inválido"]);
        }
        break;

    case 'salvar':
        $nome = htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8');
        $data_nascimento = htmlspecialchars($_POST['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8');
        $telefone = htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES, 'UTF-8');
        $classe_id = filter_input(INPUT_POST, 'classe_id', FILTER_VALIDATE_INT);

        if (!$nome || !$data_nascimento || !$telefone || !$classe_id) {
            echo json_encode(["status" => "error", "message" => "Dados inválidos"]);
        } elseif (!validarTelefone($telefone)) {
            echo json_encode(["status" => "error", "message" => "Telefone inválido"]);
        } elseif (!DateTime::createFromFormat('Y-m-d', $data_nascimento)) {
            echo json_encode(["status" => "error", "message" => "Data de nascimento inválida"]);
        } else {
            echo json_encode($aluno->salvar($nome, $data_nascimento, $telefone, $classe_id));
        }
        break;

    case 'editar':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8');
        $data_nascimento = htmlspecialchars($_POST['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8');
        $telefone = htmlspecialchars($_POST['telefone'] ?? '', ENT_QUOTES, 'UTF-8');
        $classe_id = filter_input(INPUT_POST, 'classe_id', FILTER_VALIDATE_INT);

        if ($id && $nome && $data_nascimento && $telefone && $classe_id) {
            echo json_encode($aluno->editar($id, $nome, $data_nascimento, $telefone, $classe_id));
        } else {
            echo json_encode(["status" => "error", "message" => "Dados inválidos"]);
        }
        break;

    case 'excluir':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            echo json_encode($aluno->excluir($id));
        } else {
            echo json_encode(["status" => "error", "message" => "ID inválido"]);
        }
        break;

    case 'listar_classes':  // Novo caso para listar as classes
        $classe->listar();  // Chama o método listar() da classe
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Requisição inválida"]);
        break;
}

function validarTelefone($telefone) {
    return preg_match('/^\(\d{2}\)\s\d{4,5}-\d{4}$/', $telefone);
}
?>



