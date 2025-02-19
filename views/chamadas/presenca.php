<?php
require_once '../../config/conexao.php';

$chamada_id = $_GET['chamada_id'];

// Busca os alunos matriculados na classe da chamada
$stmt = $pdo->prepare("
    SELECT a.id, a.nome
    FROM matriculas m
    JOIN alunos a ON m.aluno_id = a.id
    WHERE m.classe_id = (SELECT classe_id FROM chamadas WHERE id = ?)
");
$stmt->execute([$chamada_id]);
$alunos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Presença</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Registrar Presença</h1>
        <form id="formPresenca">
            <input type="hidden" name="chamada_id" value="<?php echo $chamada_id; ?>">
            <?php foreach ($alunos as $aluno): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="alunos[]" value="<?php echo $aluno['id']; ?>" id="aluno<?php echo $aluno['id']; ?>">
                    <label class="form-check-label" for="aluno<?php echo $aluno['id']; ?>">
                        <?php echo $aluno['nome']; ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3">Salvar</button>
        </form>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#formPresenca').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'ajax/salvar_presenca.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert('Presença registrada com sucesso!');
                        window.location.href = 'chamadas.php';
                    }
                });
            });
        });
    </script>
</body>
</html>