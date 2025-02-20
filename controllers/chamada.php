<?php
require_once '../models/chamada.php';
require_once '../config/conexao.php';

$chamada = new Chamada();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    switch ($acao) {
        case 'getCongregacoes':
            echo json_encode($chamada->getCongregacoes());
            break;

        case 'getClassesByCongregacao':
            $congregacao_id = $_POST['congregacao_id'] ?? 0;
            echo json_encode($chamada->getClassesByCongregacao($congregacao_id));
            break;

        case 'getProfessor':
            $professor_id = $_POST['professor_id'] ?? 0;
            echo json_encode($chamada->getProfessor($professor_id));
            break;

        case 'getAlunosByClasse':
            $classe_id = $_POST['classe_id'] ?? 0;
            echo json_encode($chamada->getAlunosByClasse($classe_id));
            break;

        case 'salvarChamada':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['data'], $input['classe'], $input['professor'], $input['alunos'])) {
                echo json_encode(['status' => 'error', 'message' => 'Dados inválidos!']);
                exit;
            }

            $resultado = $chamada->registrarChamada($input['data'], $input['classe'], $input['professor'], $input['alunos']);
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Ação inválida!']);
            break;
    }
}
?>




























