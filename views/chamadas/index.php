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
</head>

<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="text-center text-primary mb-4">Registro de Chamada</h2>

            <div class="row">
                <!-- Congregação -->
                <div class="col-md-6 mb-3">
                    <label for="congregacao" class="form-label fw-bold">Congregação:</label>
                    <select id="congregacao" class="form-select"></select>
                </div>

                <!-- Classe -->
                <div class="col-md-6 mb-3">
                    <label for="classe" class="form-label fw-bold">Classe:</label>
                    <select id="classe" class="form-select" disabled></select>
                </div>
            </div>

            <div class="row">
                <!-- Professor -->
                <div class="col-md-6 mb-3">
                    <label for="professor" class="form-label fw-bold">Professor:</label>
                    <input type="text" id="professor" class="form-control" readonly>
                </div>

                <!-- Data -->
                <div class="col-md-6 mb-3">
                    <label for="data" class="form-label fw-bold">Data:</label>
                    <input type="date" id="data" class="form-control">
                </div>
            </div>

            <!-- Lista de alunos -->
            <div id="alunos-container" class="mt-3"></div>

            <!-- Botões de ação -->
            <div class="d-flex justify-content-center mt-4">
                <button id="salvarChamada" class="btn btn-success btn-lg me-2">
                    <i class="bi bi-check-circle"></i> Salvar Chamada
                </button>
                <a href="../../views/dashboard.php" class="btn btn-secondary btn-lg">
                    <i class="bi bi-arrow-left-circle"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>
    $(document).ready(function() {
        // Carregar congregações com cache
        if (localStorage.getItem("congregacoes")) {
            $("#congregacao").html(localStorage.getItem("congregacoes"));
        } else {
            $.ajax({
                url: "../../controllers/chamada.php",
                type: "POST",
                data: {
                    acao: 'getCongregacoes'
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response && Array.isArray(response)) {
                        let options = "<option value=''>Escolha uma Congregação...</option>";
                        response.forEach(congregacao => {
                            options +=
                                `<option value="${congregacao.id}">${congregacao.nome}</option>`;
                        });
                        $("#congregacao").html(options);
                        localStorage.setItem("congregacoes", options); // Cache
                    } else {
                        console.error("Formato de resposta inválido para congregações:", response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao carregar congregações:", error);
                }
            });
        }

        // Carregar classes ao selecionar congregação
        $("#congregacao").change(function() {
            let congregacaoId = $(this).val();
            if (congregacaoId) {
                $.ajax({
                    url: "../../controllers/chamada.php",
                    type: "POST",
                    data: {
                        acao: 'getClassesByCongregacao',
                        congregacao_id: congregacaoId
                    },
                    success: function(classes) {
                        classes = JSON.parse(classes);
                        if (Array.isArray(classes)) {
                            let options = "<option value=''>Escolha uma Classe...</option>";
                            classes.forEach(classe => {
                                options +=
                                    `<option value="${classe.id}" data-professor="${classe.professor_id}">${classe.nome}</option>`;
                            });
                            $("#classe").html(options).prop('disabled', false);
                        } else {
                            console.error("Formato de resposta inválido para classes:",
                                classes);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar classes:", error);
                    }
                });
            } else {
                $("#classe").html("<option value=''>Escolha uma Congregação Primeiro</option>").prop(
                    'disabled', true);
            }
        });

        // Atualizar professor ao selecionar classe
        $("#classe").change(function() {
            let professorId = $("option:selected", this).data("professor");
            let classeId = $(this).val();

            if (professorId) {
                $.ajax({
                    url: "../../controllers/chamada.php",
                    type: "POST",
                    data: {
                        acao: 'getProfessor',
                        professor_id: professorId
                    },
                    success: function(professor) {
                        professor = JSON.parse(professor);
                        if (professor && professor.nome) {
                            $("#professor").val(professor.nome);
                        } else {
                            console.error("Formato de resposta inválido para professor:",
                                professor);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar professor:", error);
                    }
                });
            }

            if (classeId) {
                $.ajax({
                    url: "../../controllers/chamada.php",
                    type: "POST",
                    data: {
                        acao: 'getAlunosByClasse',
                        classe_id: classeId
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (Array.isArray(response)) {
                            let table =
                                `<table class='table table-striped'><thead><tr><th>Nome</th><th>Presente</th></tr></thead><tbody>`;
                            response.forEach(aluno => {
                                table +=
                                    `<tr><td>${aluno.nome}</td><td><input type='checkbox' class='aluno-presenca' data-id='${aluno.id}'></td></tr>`;
                            });
                            table += `</tbody></table>`;
                            $("#alunos-container").html(table);
                        } else {
                            console.error("Formato de resposta inválido para alunos:",
                                response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar alunos:", error);
                    }
                });
            }
        });

        // Salvar chamada
        $("#salvarChamada").click(function() {
            let chamadaData = {
                congregacao: $("#congregacao").val(),
                classe: $("#classe").val(),
                professor: $("option:selected", "#classe").data("professor"),
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
                        alert(response.message);
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao salvar a chamada:", error);
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