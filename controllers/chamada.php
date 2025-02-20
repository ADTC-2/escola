<?php
require_once '../config/conexao.php';
require_once '../models/Chamada.php';

$acao = $_POST['acao'] ?? null;

switch ($acao) {
    case 'getCongregacoes':
        $congregacoes = Chamada::getCongregacoes();
        echo json_encode($congregacoes);
        break;
    
    case 'getClassesByCongregacao':
        $congregacao_id = $_POST['congregacao_id'] ?? 0;
        $classes = Chamada::getClassesByCongregacao($congregacao_id);
        echo json_encode($classes);
        break;
    
    case 'getProfessor':
        $professor_id = $_POST['professor_id'] ?? 0;
        $professor = Chamada::getProfessor($professor_id);
        echo json_encode($professor);
        break;

    case 'getAlunosByClasse':
        $classe_id = $_POST['classe_id'] ?? 0;
        $alunos = Chamada::getAlunosByClasse($classe_id);
        echo json_encode($alunos);
        break;

    case 'registrarChamada':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['acao'])) {
            $data = json_decode(file_get_contents("php://input"), true);

            $classe_id = $data['classe'] ?? null;
            $professor_id = $_SESSION['usuario_id']; // Pegando o professor logado
            $data_chamada = $data['data'] ?? date('Y-m-d');

            if (!$classe_id || empty($data['alunos'])) {
                echo json_encode(["status" => "error", "message" => "Dados inválidos"]);
                exit;
            }

            // Registrar chamada
            $result = Chamada::registrarChamada($classe_id, $professor_id, $data_chamada, $data['alunos']);
            echo json_encode($result);
        }
        break;
    
    default:
        echo json_encode(["status" => "error", "message" => "Ação inválida."]);
}
?>























