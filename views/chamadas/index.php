<?php   
    require_once '../../auth/valida_sessao.php';
    require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D - Registro de Chamadas</title>
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
                    <li class="nav-item"><a class="nav-link" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alunos/index.php">Alunos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../classes/index.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="../professores/index.php">Professores</a></li>
                    <li class="nav-item"><a class="nav-link" href="../congregacao/index.php">Congregações</a></li>
                    <li class="nav-item"><a class="nav-link" href="../matriculas/index.php">Matrículas</a></li>
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

        <div class="card p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="congregacao" class="form-label">Selecione a Congregação:</label>
                    <select id="congregacao" class="form-select">
                        <option value="">Carregando...</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="classe" class="form-label">Selecione a Classe:</label>
                    <select id="classe" class="form-select" disabled>
                        <option value="">Escolha uma Congregação Primeiro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="professor" class="form-label">Professor Responsável:</label>
                    <input type="text" id="professor" class="form-control" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data" class="form-label">Data da Chamada:</label>
                    <input type="date" id="data" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <div id="alunos-container" class="mt-3">
                <!-- A tabela de alunos será carregada aqui -->
            </div>

            <button id="salvarChamada" class="btn btn-success mt-3 w-100">Salvar Chamada</button>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>
$(document).ready(function() {
    // Carregar congregações
    $.ajax({
        url: "../../controllers/chamada.php",  // Caminho para o controlador
        type: "POST",
        data: { acao: 'getCongregacoes' },    // Ação para buscar congregações
success: function(data) {
    console.log(data);  // Adicione essa linha para depurar a resposta
    try {
        let response = JSON.parse(data); 
        if (Array.isArray(response)) {
            let options = "<option value=''>Escolha uma Congregação...</option>";
            response.forEach(function(congregacao) {
                options += `<option value="${congregacao.id}">${congregacao.nome}</option>`;
            });
            $("#congregacao").html(options); 
        } else {
            alert("Erro: Não foi possível carregar as congregações.");
        }
    } catch (error) {
        console.error("Erro ao processar dados JSON:", error);
        alert("Erro ao processar dados JSON.");
    }
}

    });

// Carregar classes ao selecionar congregação
$("#congregacao").change(function() {
    let congregacaoId = $(this).val();
    console.log('Congregação selecionada:', congregacaoId); // Verificar valor da congregação
    
    if (congregacaoId) {
        $.ajax({
            url: "../../controllers/chamada.php",
            type: "POST",
            data: { acao: 'getClassesByCongregacao', congregacao_id: congregacaoId },
            success: function(classes) {
                console.log('Resposta do servidor:', classes); // Verificar resposta do servidor
                
                try {
                    classes = JSON.parse(classes);  // Tentar converter para JSON
                    
                    let options = "<option value=''>Escolha uma Classe...</option>";
                    classes.forEach(classe => {
                        options += `<option value="${classe.id}" data-professor="${classe.professor_id}">${classe.nome}</option>`;
                    });
                    $("#classe").html(options).prop('disabled', false);
                } catch (error) {
                    console.error("Erro ao processar dados JSON:", error);
                    alert("Erro ao processar dados de classes.");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro ao carregar as classes:", textStatus, errorThrown);
                alert("Erro ao carregar as classes.");
            }
        });
    } else {
        $("#classe").html("<option value=''>Escolha uma Congregação Primeiro</option>").prop('disabled', true);
    }
});


    // Atualizar professor ao selecionar classe
    $("#classe").change(function() {
        let professorId = $("option:selected", this).data("professor");
        $.ajax({
            url: "../../controllers/chamada.php",
            type: "POST",
            data: { acao: 'getProfessor', professor_id: professorId },
            success: function(professor) {
                professor = JSON.parse(professor);  
                $("#professor").val(professor.nome);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Erro ao carregar o professor.");
                console.error(textStatus, errorThrown);
            }
        });

        let classeId = $(this).val();
        if (classeId) {
            $.ajax({
                url: "../../controllers/chamada.php",
                type: "POST",
                data: { acao: 'getAlunosByClasse', classe_id: classeId },
                success: function(response) {
                    response = JSON.parse(response);  
                    let table = `<table class='table table-striped'><thead><tr><th>Nome</th><th>Presente</th></tr></thead><tbody>`;
                    response.forEach(aluno => {
                        table += `<tr><td>${aluno.nome}</td><td><input type='checkbox' class='aluno-presenca' data-id='${aluno.id}'></td></tr>`;
                    });
                    table += `</tbody></table>`;
                    $("#alunos-container").html(table);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Erro ao carregar os alunos.");
                    console.error(textStatus, errorThrown);
                }
            });
        }
    });

    // Salvar chamada
    $("#salvarChamada").click(function() {
        let chamadaData = {
            congregacao: $("#congregacao").val(),
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

        if (chamadaData.alunos.length > 0) {
            $.ajax({
                url: "../../controllers/chamada.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(chamadaData),
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status === "success") {
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert("Erro ao salvar a chamada.");
                }
            });
        } else {
            alert("Nenhum aluno foi selecionado.");
        }
    });
});
</script>

</body>

</html>
