<?php
require_once '../models/chamada.php';
require_once '../config/conexao.php';

// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Garantir que a resposta seja sempre JSON
header('Content-Type: application/json');

// Criar a instância do objeto Chamada
$chamada = new Chamada($pdo);

// Capturar erros inesperados e sempre retornar JSON
function sendErrorResponse($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Método inválido. Apenas POST é permitido.');
}

// Obtém os dados enviados
$input = json_decode(file_get_contents('php://input'), true);

// Se os dados não foram enviados em JSON, tenta com $_POST
if (!$input) {
    $input = $_POST;
}

// Verificar se 'acao' foi enviada
$acao = $input['acao'] ?? '';
if (empty($acao)) {
    sendErrorResponse('Ação não especificada.');
}

// Verificar se a conexão com o banco de dados está funcionando
if (!$pdo) {
    sendErrorResponse('Erro na conexão com o banco de dados.');
}

try {
    switch ($acao) {
        case 'getCongregacoes':
            $congregacoes = $chamada->getCongregacoes();
            echo json_encode(['status' => 'success', 'data' => $congregacoes ?: []]);
            break;

        case 'getClassesByCongregacao':
            $congregacao_id = $input['congregacao_id'] ?? 0;
            if (!$congregacao_id) {
                sendErrorResponse('ID da congregação inválido.');
            }
            $classes = $chamada->getClassesByCongregacao($congregacao_id);
            echo json_encode(['status' => 'success', 'data' => $classes ?: []]);
            break;

        case 'getAlunosByClasse':
            $classe_id = $input['classe_id'] ?? 0;
            if (!$classe_id) {
                sendErrorResponse('ID da classe inválido.');
            }
            $alunos = $chamada->getAlunosByClasse($classe_id);
            echo json_encode(['status' => 'success', 'data' => $alunos ?: []]);
            break;

        case 'salvarChamada':
            if (!isset($input['data'], $input['classe'], $input['professor'], $input['alunos'])) {
                sendErrorResponse('Dados inválidos para salvar chamada.');
            }

            $resultado = $chamada->registrarChamada($input['data'], $input['classe'], $input['professor'], $input['alunos']);
            if ($resultado['sucesso']) {
                echo json_encode(['status' => 'success', 'message' => 'Chamada registrada com sucesso.']);
            } else {
                sendErrorResponse($resultado['mensagem']);
            }
            break;

        default:
            sendErrorResponse('Ação inválida.');
    }
} catch (Exception $e) {
    sendErrorResponse('Erro interno: ' . $e->getMessage());
}
?>



