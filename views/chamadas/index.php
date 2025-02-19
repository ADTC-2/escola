<?php  
    require_once '../../auth/valida_sessao.php';
    require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/aluno.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">E.B.D - Painel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alunos/index.php">Alunos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../classes/index.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="../professores/index.php">Professores</a></li>
                    <li class="nav-item"><a class="nav-link" href="../congregacao/index.php">Congregações</a></li>
                    <li class="nav-item"><a class="nav-link" href="../matriculas/index.php">Matriculas</a></li>
                    <li class="nav-item"><a class="nav-link" href="../usuario/index.php">Usuários</a></li>
                    <li class="nav-item"><a class="nav-link active" href="../permissoes/index.php">Permissões</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Relatórios</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Registro de Chamadas</h2>
        <div class="row">
            <div class="col-md-6">
                <label for="classe">Selecione a Classe:</label>
                <select id="classe" class="form-control">
                    <option value="">Carregando...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="data">Data da Chamada:</label>
                <input type="date" id="data" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        <div class="mt-3" id="alunos-container"></div>
        <button id="salvarChamada" class="btn btn-success mt-3">Salvar Chamada</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Carregar classes
            $.get("../../controllers/chamada.php?action=getClasses", function(data) {
                let classes = JSON.parse(data);
                let options = "<option value=''>Escolha uma classe...</option>";
                classes.forEach(classe => {
                    options += `<option value="${classe.id}">${classe.nome}</option>`;
                });
                $("#classe").html(options);
            });

            // Carregar alunos ao selecionar classe
            $("#classe").change(function() {
                let classeId = $(this).val();
                if (classeId) {
                    $.get(`../../controllers/chamada.php?action=getAlunos&classe=${classeId}`, function(response) {
                        let alunos = JSON.parse(response);
                        let table = `<table class='table table-striped'><tr><th>Nome</th><th>Presente</th></tr>`;
                        alunos.forEach(aluno => {
                            table += `<tr>
                                        <td>${aluno.nome}</td>
                                        <td><input type='checkbox' class='aluno-presenca' data-id='${aluno.id}'></td>
                                      </tr>`;
                        });
                        table += `</table>`;
                        $("#alunos-container").html(table);
                    });
                }
            });

            // Salvar chamada
            $("#salvarChamada").click(function() {
                let chamadaData = {
                    classe: $("#classe").val(),
                    data: $("#data").val(),
                    alunos: []
                };
                $(".aluno-presenca").each(function() {
                    chamadaData.alunos.push({
                        id: $(this).data("id"),
                        presente: $(this).is(":checked") ? 1 : 0
                    });
                });

                $.post("../../controllers/chamada.php?action=salvar", JSON.stringify(chamadaData), function(response) {
                    alert(response);
                });
            });
        });
    </script>
</body>
</html>