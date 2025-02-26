<?php
require_once '../models/chamada.php';
require_once '../config/conexao.php';

// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Garantir que a resposta seja JSON
header('Content-Type: application/json');

// Criar a instância do objeto Chamada
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
            $congregacoes = $chamada->getCongregacoes();
            if ($congregacoes) {
                echo json_encode(['status' => 'success', 'data' => $congregacoes]);
            } else {
                sendErrorResponse('Nenhuma congregação encontrada.');
            }
            break;

        case 'getClassesByCongregacao':
            $congregacao_id = $_POST['congregacao_id'] ?? 0;
            if (!$congregacao_id) {
                sendErrorResponse('ID da congregação inválido.');
            }
            $classes = $chamada->getClassesByCongregacao($congregacao_id);
            if ($classes) {
                echo json_encode(['status' => 'success', 'data' => $classes]);
            } else {
                sendErrorResponse('Nenhuma classe encontrada para esta congregação.');
            }
            break;

        case 'getAlunosByClasse':
            $classe_id = $_POST['classe_id'] ?? 0;
            if (!$classe_id) {
                sendErrorResponse('ID da classe inválido.');
            }
            
            // Chama a função que foi adicionada na classe Chamada
            $alunos = $chamada->getAlunosByClasse($classe_id);
            
            // Verifica se alunos foram encontrados
            if ($alunos) {
                echo json_encode(['status' => 'success', 'data' => $alunos]);
            } else {
                sendErrorResponse('Nenhum aluno encontrado para esta classe.');
            }
            break;

        case 'salvarChamada':
            $input = json_decode(file_get_contents('php://input'), true);
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
            break;
    }
} catch (Exception $e) {
    sendErrorResponse('Erro interno: ' . $e->getMessage());
}
?>


