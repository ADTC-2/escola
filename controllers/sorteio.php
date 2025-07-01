<?php

// Configurações iniciais de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Inclui conexão PDO
require_once __DIR__ . '/../config/conexao.php';

// Recebe dados JSON ou POST
$input = file_get_contents('php://input');
if (!empty($input)) {
    $input = json_decode($input, true);
} else {
    $input = $_POST;
}

// Validação básica da ação
$acao = $input['acao'] ?? '';
if (empty($acao)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Ação não especificada']);
    exit;
}

// Função para validar parâmetros obrigatórios
function validarParametros(array $campos, array $dados) {
    $faltantes = [];
    foreach ($campos as $campo) {
        if (!isset($dados[$campo]) || $dados[$campo] === '') {
            $faltantes[] = $campo;
        }
    }
    if (!empty($faltantes)) {
        throw new Exception('Parâmetros obrigatórios faltando: ' . implode(', ', $faltantes));
    }
}

try {
    $pdo->beginTransaction();

    switch ($acao) {
        case 'getAlunosParaSorteio':
            validarParametros(['classe_id', 'congregacao_id'], $input);

            // Calcula datas do trimestre atual
            $hoje = new DateTime();
            $mes = (int)$hoje->format('n');
            $ano = (int)$hoje->format('Y');

            $trimestre = ceil($mes / 3);
            $inicioMes = (($trimestre - 1) * 3) + 1;

            $dataInicio = new DateTime("$ano-$inicioMes-01");
            $dataFim = (clone $dataInicio)->modify('+2 months')->modify('last day of this month');

            $stmt = $pdo->prepare("
                SELECT a.id, a.nome, c.nome AS classe_nome
                FROM alunos a
                JOIN matriculas m ON m.aluno_id = a.id AND m.status = 'ativo'
                JOIN classes c ON c.id = m.classe_id
                WHERE m.classe_id = :classe_id 
                AND m.congregacao_id = :congregacao_id
                AND a.id NOT IN (
                    SELECT p.aluno_id
                    FROM presencas p
                    JOIN chamadas ch ON ch.id = p.chamada_id
                    WHERE ch.classe_id = :classe_id
                      AND ch.congregacao_id = :congregacao_id
                      AND ch.data_chamada BETWEEN :data_inicio AND :data_fim
                    GROUP BY p.aluno_id, MONTH(ch.data_chamada)
                    HAVING SUM(p.presente = 'presente') = 0
                )
                ORDER BY a.nome
            ");

            $stmt->execute([
                ':classe_id' => (int)$input['classe_id'],
                ':congregacao_id' => (int)$input['congregacao_id'],
                ':data_inicio' => $dataInicio->format('Y-m-d'),
                ':data_fim' => $dataFim->format('Y-m-d'),
            ]);

            echo json_encode([
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        case 'realizarSorteio':
            validarParametros(['alunos_ids', 'classe_id', 'congregacao_id'], $input);

            $alunosIds = is_array($input['alunos_ids']) 
                ? array_map('intval', $input['alunos_ids'])
                : array_map('intval', explode(',', $input['alunos_ids']));

            if (empty($alunosIds)) {
                throw new Exception("Nenhum aluno válido selecionado");
            }

            // Sorteia aleatoriamente um aluno
            $ganhadorId = $alunosIds[array_rand($alunosIds)];

            // Obtém dados do ganhador
            $stmt = $pdo->prepare("
                SELECT a.id, a.nome, c.nome AS classe_nome
                FROM alunos a
                LEFT JOIN classes c ON c.id = :classe_id
                WHERE a.id = :aluno_id
            ");
            $stmt->execute([
                ':aluno_id' => $ganhadorId,
                ':classe_id' => (int)$input['classe_id']
            ]);
            $ganhador = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ganhador) {
                throw new Exception("Aluno sorteado não encontrado");
            }

            // Registra o sorteio
            $stmt = $pdo->prepare("
                INSERT INTO sorteios 
                (aluno_id, classe_id, congregacao_id, data_sorteio)
                VALUES (:aluno_id, :classe_id, :congregacao_id, NOW())
            ");
            $stmt->execute([
                ':aluno_id' => $ganhadorId,
                ':classe_id' => (int)$input['classe_id'],
                ':congregacao_id' => (int)$input['congregacao_id']
            ]);

            $pdo->commit();

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'ganhador' => $ganhador,
                    'data_sorteio' => date('d/m/Y H:i:s')
                ]
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Ação não reconhecida']);
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Erro no banco de dados',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}



