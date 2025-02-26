<?php
require_once '../models/chamada.php';
require_once '../config/conexao.php';

// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Garantir que a resposta seja JSON
header('Content-Type: application/json');

$chamada = new Chamada($pdo);

// Capturar erros inesperados e sempre retornar JSON
function sendErrorResponse($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

// Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Método inválido. Apenas POST é permitido.');
}

// Garantir que 'acao' foi enviado
$acao = $_POST['acao'] ?? '';
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
            echo json_encode(['status' => 'success', 'data' => $chamada->getCongregacoes()]);
            break;

        case 'getClassesByCongregacao':
            $congregacao_id = $_POST['congregacao_id'] ?? 0;
            if (!$congregacao_id) {
                sendErrorResponse('ID da congregação inválido.');
            }
            echo json_encode(['status' => 'success', 'data' => $chamada->getClassesByCongregacao($congregacao_id)]);
            break;

        case 'getProfessor':
            $professor_id = $_POST['professor_id'] ?? 0;
            if (!$professor_id) {
                sendErrorResponse('ID do professor inválido.');
            }
            echo json_encode($chamada->getProfessor($professor_id));
            break;

        case 'getAlunosByClasse':
            $classe_id = $_POST['classe_id'] ?? 0;
            if (!$classe_id) {
                sendErrorResponse('ID da classe inválido.');
            }
            echo json_encode(['status' => 'success', 'data' => $chamada->getAlunosByClasse($classe_id)]);
            break;

        case 'salvarChamada':
            // Captura o JSON enviado
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar entrada
            if (!isset($input['data'], $input['classe'], $input['professor'], $input['alunos'])) {
                sendErrorResponse('Dados inválidos para salvar chamada.');
            }

            // Registrar chamada
            $resultado = $chamada->registrarChamada($input['data'], $input['classe'], $input['professor'], $input['alunos']);
            echo json_encode($resultado);
            break;

        default:
            sendErrorResponse('Ação inválida.');
            break;
    }
} catch (Exception $e) {
    sendErrorResponse('Erro interno: ' . $e->getMessage());
}
?>































