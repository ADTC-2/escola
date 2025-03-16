<?php
// Incluir o arquivo de conexão com o banco de dados
require_once('../config/conexao.php');

// Verificar se a requisição é uma GET para o relatório
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obter os parâmetros da requisição
    $classe = isset($_GET['classe']) ? $_GET['classe'] : '';
    $data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
    $data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

    // Verificar se todos os parâmetros necessários foram recebidos
    if ($classe && $data_inicio && $data_fim) {
        // Query para buscar dados de frequência
        $sql = "
            SELECT 
                c.data, 
                COUNT(CASE WHEN p.presente = 'presente' THEN 1 END) AS alunos_presentes,
                COUNT(CASE WHEN p.presente = 'ausente' THEN 1 END) AS alunos_ausentes,
                COUNT(CASE WHEN p.presente = 'justificado' THEN 1 END) AS alunos_justificados,
                SUM(c.total_biblias) AS biblias,
                SUM(c.total_revistas) AS revistas,
                SUM(c.total_visitantes) AS visitantes
            FROM chamadas c
            LEFT JOIN presencas p ON p.chamada_id = c.id
            WHERE c.classe_id = :classe
            AND c.data BETWEEN :data_inicio AND :data_fim
            GROUP BY c.data
            ORDER BY c.data;
        ";

        try {
            // Preparar a consulta usando PDO
            $stmt = $pdo->prepare($sql);

            // Bind dos parâmetros
            $stmt->bindParam(':classe', $classe, PDO::PARAM_INT);
            $stmt->bindParam(':data_inicio', $data_inicio, PDO::PARAM_STR);
            $stmt->bindParam(':data_fim', $data_fim, PDO::PARAM_STR);

            // Executar a consulta
            $stmt->execute();

            // Obter os resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Armazenar os dados em um array
            $dados = ['total_frequencia' => $resultados];

            // Retornar os dados no formato JSON
            echo json_encode($dados);

        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro na consulta: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Parâmetros insuficientes.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
}
?>