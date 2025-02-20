<?php
require_once "../config/conexao.php"; // Inclui a configuração de conexão
require_once '../../models/chamada.php';

$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

switch ($acao) {
    case 'getCongregacoes':
        $congregacoes = Chamada::getCongregacoes();
        echo json_encode($congregacoes);
        break;

    case 'getClasses':
        $congregacaoId = isset($_GET['congregacao']) ? $_GET['congregacao'] : 0;
        $classes = Chamada::getClasses($congregacaoId);
        echo json_encode($classes);
        break;

    case 'getProfessor':
        $professorId = isset($_GET['professor_id']) ? $_GET['professor_id'] : 0;
        $professor = Chamada::getProfessor($professorId);
        echo json_encode($professor);
        break;

    case 'getAlunos':
        $classeId = isset($_GET['classe']) ? $_GET['classe'] : 0;
        $alunos = Chamada::getAlunos($classeId);
        echo json_encode($alunos);
        break;

    case 'salvar':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = Chamada::salvarChamada($data);
        echo json_encode($result);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Ação não encontrada.']);
        break;
}
?>