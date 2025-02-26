<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamada de Alunos</title>
    <!-- Bootstrap e FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h4>Registrar Chamada</h4>
            </div>
            <div class="card-body">
                <form id="formChamada">
                    <!-- Seleção de Congregação -->
                    <div class="mb-3">
                        <label for="congregacao" class="form-label">Congregação</label>
                        <select class="form-control" id="congregacao" required>
                            <option value="">Selecione a Congregação</option>
                        </select>
                    </div>

                    <!-- Seleção de Classe -->
                    <div class="mb-3">
                        <label for="classe" class="form-label">Classe</label>
                        <select class="form-control" id="classe" required disabled>
                            <option value="">Selecione a Classe</option>
                        </select>
                    </div>

                    <!-- Tabela de Alunos -->
                    <div id="alunos-container" class="mb-3"></div>

                    <!-- Data da Chamada -->
                    <div class="mb-3">
                        <label for="data_chamada" class="form-label">Data da Chamada</label>
                        <input type="date" class="form-control" id="data_chamada" required>
                    </div>

                    <!-- Professor (hidden) -->
                    <input type="hidden" id="professor_id" value="{{ professor_id }}">

                    <button type="submit" class="btn btn-primary">Salvar Chamada</button>
                    <a href="../../views/dashboard.php" class="btn btn-secondary">Voltar</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidade dinâmica -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Função para carregar congregações
    function carregarCongregacoes(selectedId = '') {
        $.post('../../controllers/congregacao.php', {
            acao: 'listar'
        }, function(response) {
            if (response.sucesso) {
                let options = '<option value="">Selecione</option>';
                response.data.forEach(c => {
                    options +=
                        `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
                });
                $('#congregacao').html(options);
                $('#congregacao_editar').html(options);
            } else {
                console.error("Erro ao carregar congregações:", response.mensagem);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição das congregações:", textStatus, errorThrown);
        });
    }

 // Carregar as classes ao selecionar a congregação
$('#congregacao').change(function() {
    let congregacaoId = $(this).val();
    if (congregacaoId) {
        $.ajax({
            url: '../../controllers/chamada.php',  // URL para o controlador PHP
            type: 'POST',  // Usar POST para enviar dados
            data: { 
                acao: 'getClassesByCongregacao',  // Ação para pegar as classes
                congregacao_id: congregacaoId // Enviar o ID da congregação via POST
            },
            dataType: 'json', // Certifique-se de que o tipo de dado é JSON
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let options = '<option value="">Selecione a Classe</option>';
                    response.data.forEach(function(classe) {
                        options += `<option value="${classe.id}">${classe.nome}</option>`;
                    });
                    $("#classe").html(options).prop('disabled', false);  // Preenche o select e habilita
                } else {
                    console.error("Nenhuma classe encontrada ou erro na resposta:", response);
                    $("#classe").html('<option value="">Nenhuma classe disponível</option>').prop('disabled', true);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let errorMsg = "Erro ao carregar classes";
                if (jqXHR.responseJSON && jqXHR.responseJSON.mensagem) {
                    errorMsg = jqXHR.responseJSON.mensagem;
                }
                console.error(errorMsg, textStatus, errorThrown);
                $("#classe").html('<option value="">' + errorMsg + '</option>').prop('disabled', true);  // Exibe mensagem de erro
            }
        });
    } else {
        $("#classe").prop('disabled', true).html('<option value="">Selecione a Classe</option>');
    }
});

// Carregar alunos ao selecionar a classe
$('#classe').change(function() {
    let classeId = $(this).val();
    if (classeId) {
        $.ajax({
            url: '../../controllers/chamada.php',  // Verifique o caminho da URL
            type: 'POST',
            data: {
                acao: 'getAlunosByClasse',
                classe_id: classeId
            },
            dataType: 'json',  // Garante que a resposta seja interpretada como JSON
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let table = `<table class="table table-striped">
                                    <thead><tr><th>Nome</th><th>Presente</th></tr></thead><tbody>`;
                    response.data.forEach(function(aluno) {
                        table += `<tr><td>${aluno.nome}</td><td><input type="checkbox" class="aluno-presenca" data-id="${aluno.id}"></td></tr>`;
                    });
                    table += `</tbody></table>`;
                    $("#alunos-container").html(table);
                } else {
                    console.error("Erro ao carregar alunos:", response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição dos alunos:", textStatus, errorThrown);
                alert("Ocorreu um erro ao tentar carregar os alunos. Tente novamente.");
            }
        });
    } else {
        $("#alunos-container").empty();
    }
});

        // Enviar dados para salvar a chamada
        $('#formChamada').submit(function(event) {
            event.preventDefault();
            let dataChamada = $('#data_chamada').val();
            let classeId = $('#classe').val();
            let professorId = $('#professor_id').val();
            let presencas = [];
            $('.aluno-presenca').each(function() {
                presencas.push({
                    id: $(this).data('id'),
                    presente: $(this).is(':checked')
                });
            });

            $.ajax({
                url: '../../controllers/chamada.php',
                type: 'POST',
                data: JSON.stringify({
                    acao: 'salvarChamada',
                    data: dataChamada,
                    classe: classeId,
                    professor: professorId,
                    alunos: presencas
                }),
                contentType: "application/json",
                success: function(response) {
                    if (response.sucesso) {
                        alert('Chamada salva com sucesso!');
                        window.location.href = "/dashboard"; // Atualize para a URL correta
                    } else {
                        alert('Erro ao salvar a chamada.');
                        console.error("Erro ao salvar a chamada:", response.mensagem);
                    }
                },
                error: function() {
                    alert('Erro ao salvar a chamada.');
                }
            });
    });

    // Inicializar carregando as congregações
    carregarCongregacoes();
});
</script>
</body>
</html>
