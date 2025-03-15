<?php
require_once '../models/chamada.php';
require_once '../config/conexao.php';

//header('Content-Type: application/json');

// Criar a instância do objeto Chamada
$chamada = new Chamada($pdo);

function sendErrorResponse($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

// Verificar se a requisição foi feita via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Método inválido. Apenas POST é permitido.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$acao = $input['acao'] ?? '';
if (empty($acao)) {
    sendErrorResponse('Ação não especificada.');
}

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
            $congregacao_id = $input['congregacao_id'] ?? 0;
            if (!$classe_id || !$congregacao_id) {
                sendErrorResponse('IDs da classe ou congregação inválidos.');
            }
            $alunos = $chamada->getAlunosByClasse($classe_id, $congregacao_id);
            echo json_encode(['status' => 'success', 'data' => $alunos ?: []]);
            break;

            case 'salvarChamada':
                if (!isset($input['data'], $input['classe'], $input['professor'], $input['alunos'], $input['oferta_classe'])) {
                    sendErrorResponse('Dados inválidos para salvar chamada.');
                }
                
                $resultado = $chamada->registrarChamada(
                    $input['data'],
                    $input['classe'],
                    $input['professor'],
                    $input['alunos'],
                    $input['oferta_classe']
                );
            
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





