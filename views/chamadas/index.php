<?php
require_once '../../views/includes/header.php';
session_start();
$usuario_id = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamada de Alunos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .presenca-option {
            cursor: pointer;
        }
        .presenca-option:hover {
            background-color: #f8f9fa;
        }
        .table-presencas th {
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-clipboard-check me-2"></i>Registrar Chamada</h4>
        </div>
        <div class="card-body">
            <form id="formChamada">
                <input type="hidden" id="professor_id" value="<?php echo $usuario_id; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="congregacao" class="form-label">Congregação <span class="text-danger">*</span></label>
                        <select class="form-select" id="congregacao" required>
                            <option value="">Selecione a Congregação</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="classe" class="form-label">Classe <span class="text-danger">*</span></label>
                        <select class="form-select" id="classe" required disabled>
                            <option value="">Selecione a Classe</option>
                        </select>
                    </div>
                </div>

                <div id="alunos-container" class="mb-3"></div>

                <div class="alert alert-info mb-3">
                    <span class="fw-bold text-success" id="totalPresentesLabel">Total de Presentes: 0</span>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="data_chamada" class="form-label">Data da Chamada <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="data_chamada" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="trimestre" class="form-label">Trimestre <span class="text-danger">*</span></label>
                        <select class="form-select" id="trimestre" required>
                            <option value="">Selecione o Trimestre</option>
                            <option value="1">1º Trimestre</option>
                            <option value="2">2º Trimestre</option>
                            <option value="3">3º Trimestre</option>
                            <option value="4">4º Trimestre</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="oferta_classe" class="form-label">Oferta da Classe (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="oferta_classe" placeholder="0,00">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="total_visitantes" class="form-label">Total de Visitantes</label>
                        <input type="number" class="form-control" id="total_visitantes" value="0" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="total_biblias" class="form-label">Total de Bíblias Levadas</label>
                        <input type="number" class="form-control" id="total_biblias" value="0" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="total_revistas" class="form-label">Total de Revistas Levadas</label>
                        <input type="number" class="form-control" id="total_revistas" value="0" min="0" required>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="../../views/dashboard.php" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salvar Chamada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Configura data atual como padrão
    let today = new Date().toISOString().split('T')[0];
    $('#data_chamada').val(today);

    // Carrega congregações
    function carregarCongregacoes() {
        $.ajax({
            url: '../../controllers/chamada.php',
            type: 'POST',
            data: { acao: 'getCongregacoes' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let options = '<option value="">Selecione a Congregação</option>';
                    response.data.forEach(c => {
                        options += `<option value="${c.id}">${c.nome}</option>`;
                    });
                    $('#congregacao').html(options);
                } else {
                    Swal.fire('Erro', 'Não foi possível carregar as congregações', 'error');
                }
            },
            error: function() {
                Swal.fire('Erro', 'Falha na comunicação com o servidor', 'error');
            }
        });
    }

    // Atualiza contador de presentes
    function atualizarTotalPresentes() {
        let total = $('input.aluno-presenca:checked').length;
        $('#totalPresentesLabel').html(`<i class="fas fa-users me-1"></i> Total de Presentes: ${total}`);
    }

    // Quando muda a congregação
    $('#congregacao').change(function() {
            let congregacaoId = $(this).val();
            $('#alunos-container').html('');
            $('#totalPresentesLabel').html('<i class="fas fa-users me-1"></i> Total de Presentes: 0');
            
            if (congregacaoId) {
                $.ajax({
                    url: '../../controllers/chamada.php',
                    type: 'POST',
                    data: { 
                        acao: 'getClassesByCongregacao',
                        congregacao_id: congregacaoId 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="">Selecione a Classe</option>';
                            response.data.forEach(classe => {
                                options += `<option value="${classe.id}">${classe.nome}</option>`;
                            });
                            $("#classe").html(options).prop('disabled', false);
                        } else {
                            $("#classe").html('<option value="">Nenhuma classe disponível</option>').prop('disabled', true);
                            Swal.fire('Aviso', 'Nenhuma classe encontrada para esta congregação', 'info');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro', 'Falha ao carregar classes', 'error');
                    }
                });
            } else {
                $("#classe").html('<option value="">Selecione a Classe</option>').prop('disabled', true);
            }
        });

    // Quando muda a classe
    $('#classe').change(function () {
        let classeId = $(this).val();
        let congregacaoId = $('#congregacao').val();
        let trimestre = $('#trimestre').val();

        // Verificações obrigatórias
        if (!trimestre) {
            Swal.fire('Atenção', 'Selecione o trimestre antes de buscar os alunos.', 'warning');
            $('#classe').val(''); // opcional: limpa seleção da classe
            return;
        }

        if (classeId) {
            $.ajax({
                url: '../../controllers/chamada.php',
                type: 'POST',
                data: {
                    acao: 'getAlunosByClasse',
                    classe_id: classeId,
                    congregacao_id: congregacaoId,
                    trimestre: trimestre
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#alunos-container').html(`
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                    `);
                },
                success: function (response) {
                    if (response.status === 'success' && response.data.data.length > 0) {
                        let alunosHtml = `
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-presencas">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Aluno</th>
                                            <th class="text-center">Presente</th>
                                            <th class="text-center">Falta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        response.data.data.forEach(aluno => {
                            alunosHtml += `
                                <tr class="presenca-option">
                                    <td>${aluno.nome}</td>
                                    <td class="text-center">
                                        <input type="radio" name="presenca_${aluno.id}" class="aluno-presenca" data-id="${aluno.id}" value="presente" checked>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="presenca_${aluno.id}" class="aluno-falta" data-id="${aluno.id}" value="falta">
                                    </td>
                                </tr>
                            `;
                        });

                        alunosHtml += `</tbody></table></div>`;
                        $('#alunos-container').html(alunosHtml);

                        // Marca todos como presentes
                        $('input.aluno-presenca').prop('checked', true);
                        atualizarTotalPresentes();

                        // Clique na linha marca como presente
                        $('.presenca-option').click(function (e) {
                            if (!$(e.target).is('input[type="radio"]')) {
                                $(this).find('input.aluno-presenca').prop('checked', true).trigger('change');
                            }
                        });

                        // Atualiza total ao trocar presença/falta
                        $('input[type="radio"]').off('change').on('change', atualizarTotalPresentes);

                    } else {
                        $('#alunos-container').html('<div class="alert alert-warning">Nenhum aluno matriculado nesta classe.</div>');
                    }
                },
                error: function () {
                    Swal.fire('Erro', 'Falha ao carregar alunos', 'error');
                }
            });
        } else {
            $('#alunos-container').html('');
        }
    });


    // Envio do formulário
    $('#formChamada').submit(function(e) {
        e.preventDefault();

        // Validações básicas
        if (!$('#congregacao').val()) {
            Swal.fire('Atenção', 'Selecione uma congregação', 'warning');
            return;
        }

        if (!$('#classe').val()) {
            Swal.fire('Atenção', 'Selecione uma classe', 'warning');
            return;
        }

        if (!$('#data_chamada').val()) {
            Swal.fire('Atenção', 'Informe a data da chamada', 'warning');
            return;
        }

        if (!$('#trimestre').val()) {
            Swal.fire('Atenção', 'Selecione o trimestre', 'warning');
            return;
        }

        // Coleta presenças
        let presencas = [];
        $('input[type="radio"]:checked').each(function() {
            presencas.push({
                id: $(this).data('id'),
                status: $(this).val()
            });
        });

        // Prepara dados para envio
        let data = {
            acao: 'salvarChamada',
            data: $('#data_chamada').val(),
            classe: $('#classe').val(),
            professor: $('#professor_id').val(),
            trimestre: $('#trimestre').val(),
            alunos: presencas,
            oferta_classe: $('#oferta_classe').val() || 0,
            total_visitantes: $('#total_visitantes').val() || 0,
            total_biblias: $('#total_biblias').val() || 0,
            total_revistas: $('#total_revistas').val() || 0
        };

        // Envia para o servidor
        $.ajax({
            url: '../../controllers/chamada.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "../chamadas/index.php";
                    });
                } else {
                    Swal.fire('Erro', response.message || 'Erro ao salvar chamada', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Erro na comunicação com o servidor';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Erro', errorMsg, 'error');
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Chamada');
            }
        });
    });

    // Carrega congregações ao iniciar
    carregarCongregacoes();
});
</script>
</body>
</html>



