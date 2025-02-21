<?php
include_once '../config/conexao.php';
include_once '../models/matricula.php';
include_once '../models/aluno.php';
include_once '../models/classe.php';

class MatriculaController {
    private $matricula;
    private $aluno;
    private $classe;

    public function __construct($pdo) {
        if (!$pdo) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Falha na conexão com o banco de dados']);
            exit;
        }
        $this->matricula = new Matricula($pdo);
        $this->aluno = new Aluno($pdo);
        $this->classe = new Classe($pdo);
    }

    public function listar() {
        $result = $this->matricula->listarMatriculas();
        echo json_encode(['sucesso' => true, 'matriculas' => $result]);
    }

    public function cadastrar() {
        if (isset($_POST['aluno_id'], $_POST['classe_id'], $_POST['trimestre'])) {
            $aluno_id = $_POST['aluno_id'];
            $classe_id = $_POST['classe_id'];
            $trimestre = $_POST['trimestre'];

            // Verificação simples dos tipos dos dados
            if (!is_numeric($aluno_id) || !is_numeric($classe_id) || !is_numeric($trimestre)) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
                return;
            }

            $result = $this->matricula->cadastrarMatricula($aluno_id, $classe_id, $trimestre);
            echo json_encode(['sucesso' => $result]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados faltando']);
        }
    }

    public function excluir() {
        if (isset($_POST['matricula_id']) && is_numeric($_POST['matricula_id'])) {
            $matricula_id = $_POST['matricula_id'];
            $result = $this->matricula->excluirMatricula($matricula_id);
            echo json_encode(['sucesso' => $result]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID da matrícula inválido']);
        }
    }

    public function editarMatricula() {
        if (isset($_POST['matricula_id'], $_POST['aluno_id'], $_POST['classe_id'], $_POST['trimestre'])) {
            $matricula_id = $_POST['matricula_id'];
            $aluno_id = $_POST['aluno_id'];
            $classe_id = $_POST['classe_id'];
            $trimestre = $_POST['trimestre'];
    
            if (!is_numeric($matricula_id) || !is_numeric($aluno_id) || !is_numeric($classe_id) || !is_numeric($trimestre)) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
                return;
            }
    
            // Chama o método de edição da matrícula no model
            $result = $this->matricula->editarMatricula($matricula_id, $aluno_id, $classe_id, $trimestre);
    
            if ($result) {
                // Obter a matrícula editada para retornar os dados completos
                $matricula = $this->matricula->obterMatriculaPorId($matricula_id);
                echo json_encode(['sucesso' => true, 'matricula' => $matricula]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao editar matrícula']);
            }
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados faltando']);
        }
    }
    
    public function carregarMatricula() {
        if (isset($_POST['matricula_id']) && is_numeric($_POST['matricula_id'])) {
            $matricula_id = $_POST['matricula_id'];
            $matricula = $this->matricula->obterMatriculaPorId($matricula_id);
            if ($matricula) {
                echo json_encode(['sucesso' => true, 'matricula' => $matricula]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Matrícula não encontrada']);
            }
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID da matrícula inválido']);
        }
    }
    

    public function listarAlunos() {
        $result = $this->aluno->listar();
        echo json_encode(['sucesso' => true, 'data' => $result]);
    }

    public function listarClasses() {
        $result = $this->classe->listarClasses();
        echo json_encode(['sucesso' => true, 'data' => $result]);
    }
}

// Criar instância do controller com a conexão existente
$controller = new MatriculaController($pdo);

// Captura a ação via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    switch ($_POST['acao']) {
        case 'listarMatriculas':
            $controller->listar();
            break;
        case 'matricular':
            $controller->cadastrar();
            break;
        case 'excluirMatricula':
            $controller->excluir();
            break;
        case 'editarMatricula':
            $controller->editarMatricula();
            break;
        case 'carregarMatricula':
            $controller->carregarMatricula();
            break;
        case 'listarAlunos':
            $controller->listarAlunos();
            break;
        case 'listarClasses':
            $controller->listarClasses();
            break;
        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
            break;
    }
}
?>







