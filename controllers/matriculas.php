<?php
require_once '../config/conexao.php';
require_once '../models/matricula.php';

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$model = new MatriculaModel($pdo);

switch ($acao) {
    case 'listar':
        $result = $model->listar();
        // Modificando a resposta para garantir o formato esperado pela view
        if ($result) {
            $matriculas = [];
            foreach ($result as $matricula) {
                // Supondo que o modelo retorna as matrículas com informações completas
                $matriculas[] = [
                    'id' => $matricula['id'],
                    'aluno' => $matricula['aluno_nome'],  // Altere para o nome correto
                    'classe' => $matricula['classe_nome'],  // Altere para o nome correto
                    'congregacao' => $matricula['congregacao_nome'],  // Altere para o nome correto
                    'usuario' => $matricula['usuario_nome'],  // Altere para o nome correto
                    'data' => $matricula['data'],  // Formato de data já ajustado no modelo
                    'trimestre' => $matricula['trimestre'],
                    'status' => $matricula['status'] // Altere conforme o status correto
                ];
            }
            echo json_encode(['sucesso' => true, 'data' => $matriculas]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma matrícula encontrada.']);
        }
        break;

    case 'cadastrar':
        $aluno_id = $_POST['aluno_id'] ?? '';
        $classe_id = $_POST['classe_id'] ?? '';
        $congregacao_id = $_POST['congregacao_id'] ?? '';
        $trimestre = $_POST['trimestre'] ?? '';

        // Verificar se todos os dados necessários foram fornecidos
        if (empty($aluno_id) || empty($classe_id) || empty($congregacao_id) || empty($trimestre)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos são obrigatórios!']);
            break;
        }

        $sucesso = $model->cadastrar($aluno_id, $classe_id, $congregacao_id, $trimestre);
        echo json_encode(['sucesso' => $sucesso, 'mensagem' => $sucesso ? 'Matrícula cadastrada com sucesso!' : 'Erro ao cadastrar matrícula.']);
        break;

    case 'editar':
        $id = $_POST['id'] ?? '';
        $aluno_id = $_POST['aluno_id'] ?? '';  // Modificando para aceitar o aluno_id
        $classe_id = $_POST['classe_id'] ?? '';
        $congregacao_id = $_POST['congregacao_id'] ?? '';
        $trimestre = $_POST['trimestre'] ?? '';

        // Verificar se todos os dados necessários foram fornecidos
        if (empty($id) || empty($aluno_id) || empty($classe_id) || empty($congregacao_id) || empty($trimestre)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos são obrigatórios!']);
            break;
        }

        $sucesso = $model->editar($id, $aluno_id, $classe_id, $congregacao_id, $trimestre);
        echo json_encode(['sucesso' => $sucesso, 'mensagem' => $sucesso ? 'Matrícula editada com sucesso!' : 'Erro ao editar matrícula.']);
        break;

    case 'excluir':
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID da matrícula é obrigatório!']);
            break;
        }
        
        $sucesso = $model->excluir($id);
        echo json_encode(['sucesso' => $sucesso, 'mensagem' => $sucesso ? 'Matrícula excluída com sucesso!' : 'Erro ao excluir matrícula.']);
        break;

    case 'buscar':
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID da matrícula é obrigatório!']);
            break;
        }

        // Buscar matrícula específica
        $matricula = $model->buscar($id);
        if ($matricula) {
            echo json_encode(['sucesso' => true, 'data' => $matricula]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Matrícula não encontrada.']);
        }
        break;

    case 'carregarSelects':
        // Carregar os dados necessários para os selects
        $alunos = $model->listarAlunos(); // Método que retorna os alunos
        $classes = $model->listarClasses(); // Método que retorna as classes
        $congregacoes = $model->listarCongregacoes(); // Método que retorna as congregações
        $usuarios = $model->listarUsuarios(); // Método que retorna os professores (usuários)

        // Preparar as opções para os selects
        $response = [
            'alunos' => $alunos,
            'classes' => $classes,
            'congregacoes' => $congregacoes,
            'usuarios' => $usuarios
        ];

        echo json_encode(['sucesso' => true, 'data' => $response]);
        break;

    default:
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
}
?>










