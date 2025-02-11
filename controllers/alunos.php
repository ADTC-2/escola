<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../models/Aluno.php';
require_once '../config/conexao.php';

$aluno = new Aluno($pdo);

// Função para retornar resposta padronizada em JSON
function respostaJson($status, $mensagem = '', $dados = null) {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => $status, 
        "mensagem" => $mensagem, 
        "dados" => $dados
    ]);
    exit();
}

// Manipulação das requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    $dados = [
        'id' => $_POST['id'] ?? null,
        'nome' => trim($_POST['nome'] ?? ''),
        'data_nascimento' => $_POST['data_nascimento'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'email' => $_POST['email'] ?? '',
        'endereco' => trim($_POST['endereco'] ?? ''),
        'congregacao_id' => $_POST['congregacao_id'] ?? null
    ];

    switch ($acao) {
        case 'cadastrar':
            if ($aluno->cadastrar($dados['nome'], $dados['data_nascimento'], $dados['telefone'], $dados['email'], $dados['endereco'], $dados['congregacao_id'])) {
                respostaJson("sucesso", "Aluno cadastrado com sucesso!");
            }
            respostaJson("erro", "Erro ao cadastrar aluno.");
            break;

        case 'editar':
            if ($dados['id'] && $aluno->atualizar($dados['id'], $dados['nome'], $dados['data_nascimento'], $dados['telefone'], $dados['email'], $dados['endereco'], $dados['congregacao_id'])) {
                respostaJson("sucesso", "Aluno atualizado com sucesso!");
            }
            respostaJson("erro", "Erro ao atualizar aluno.");
            break;

        case 'excluir':
            if ($dados['id'] && $aluno->excluir($dados['id'])) {
                respostaJson("sucesso", "Aluno excluído com sucesso!");
            }
            respostaJson("erro", "Erro ao excluir aluno.");
            break;

        default:
            respostaJson("erro", "Ação inválida.");
            break;
    }
}

// Manipulação das requisições GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $alunos = $aluno->listar();
    respostaJson("sucesso", "Lista de alunos", $alunos);
}
?>


