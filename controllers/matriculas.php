<?php
require_once '../config/conexao.php';
require_once '../models/matricula.php';

class MatriculaController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Matricula($pdo);
    }

    // Listar todas as matrículas
    public function listarMatriculas() {
        try {
            $matriculas = $this->model->listarMatriculas();
            echo json_encode(['sucesso' => true, 'dados' => $matriculas]);
        } catch (Exception $e) {
            error_log("Erro no listarMatriculas (Controller): " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar matrículas.']);
        }
    }

    // Criar uma nova matrícula
    public function criarMatricula($data) {
        // Verificar se todos os campos essenciais foram preenchidos
        if (empty($data['aluno_id']) || empty($data['classe_id']) || empty($data['congregacao_id']) || empty($data['status']) || empty($data['professor_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.']);
            return;
        }
    
        try {
            $this->model->criarMatricula($data);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Matrícula criada com sucesso.']);
        } catch (Exception $e) {
            error_log("Erro ao criar matrícula (Controller): " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao criar matrícula.']);
        }
    }
    

    // Atualizar uma matrícula existente
    public function atualizarMatricula($id, $data) {
        // Verificar se todos os campos essenciais foram preenchidos
        if (empty($data['aluno_id']) || empty($data['classe_id']) || empty($data['congregacao_id']) || empty($data['status'])|| empty($data['professor_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.']);
            return;
        }
    
        try {
            $this->model->atualizarMatricula($id, $data);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Matrícula atualizada com sucesso.']);
        } catch (Exception $e) {
            error_log("Erro ao atualizar matrícula (Controller): " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar matrícula.']);
        }
    }

    // Excluir uma matrícula
    public function excluirMatricula($id) {
        // Verificar se o ID é válido
        if (!is_numeric($id) || empty($id)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
            return;
        }
    
        try {
            $this->model->excluirMatricula($id);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Matrícula excluída com sucesso.']);
        } catch (Exception $e) {
            error_log("Erro ao excluir matrícula (Controller): " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir matrícula.']);
        }
    }
    
    // Carregar selects para o formulário (alunos, classes, congregações, usuários)
    public function carregarSelects() {
        try {
            // Chama o modelo para carregar as opções dos selects
            $dados = $this->model->carregarSelects();
            echo json_encode(['sucesso' => true, 'dados' => $dados]);
        } catch (Exception $e) {
            error_log("Erro no carregarSelects (Controller): " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao carregar dados dos selects.']);
        }
    }
}

// Verifica a ação na requisição e chama o método adequado
if (isset($_GET['acao'])) {
    $controller = new MatriculaController($pdo);

    switch ($_GET['acao']) {
        case 'listarMatriculas':
            $controller->listarMatriculas();
            break;
        case 'criarMatricula':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                $controller->criarMatricula($data);
            }
            break;
        case 'atualizarMatricula':
            if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $data = json_decode(file_get_contents("php://input"), true);
                $id = $_GET['id'];
                $controller->atualizarMatricula($id, $data);
            }
            break;
        case 'excluirMatricula':
            $id = $_GET['id'];
            $controller->excluirMatricula($id);
            break;
        case 'carregarSelects':
            $controller->carregarSelects();
            break;
        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
            break;
    }
}
?>

