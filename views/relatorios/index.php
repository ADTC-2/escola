<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Frequência</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Relatório de Frequência</h1>
        <form id="formRelatorio" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="congregacao" class="form-label">Congregação</label>
                    <select class="form-control" id="congregacao" name="congregacao" required>
                        <!-- Opções serão preenchidas dinamicamente -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="classe" class="form-label">Classe</label>
                    <select class="form-control" id="classe" name="classe" required>
                        <!-- Opções serão preenchidas dinamicamente -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                </div>
                <div class="col-md-3">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Gerar Relatório</button>
        </form>

        <div class="row">
            <div class="col-md-6">
                <canvas id="graficoFrequencia"></canvas>
            </div>
            <div class="col-md-6">
                <table id="tabelaRelatorio" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Alunos Presentes</th>
                            <th>Alunos Ausentes</th>
                            <th>Visitantes</th>
                            <th>Total Frequência</th>
                            <th>Bíblias</th>
                            <th>Revistas</th>
                            <th>Ofertas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados serão preenchidos via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Carregar as congregações
            function carregarCongregacoes(selectedId = '') {
                $.post('../../controllers/chamada.php', { acao: 'getCongregacoes' }, function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">Selecione</option>';
                        response.data.forEach(c => {
                            options += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nome}</option>`;
                        });
                        $('#congregacao').html(options);
                    } else {
                        console.error("Erro ao carregar congregações:", response.message);
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
                        url: '../../controllers/chamada.php',
                        type: 'POST',
                        data: { acao: 'getClassesByCongregacao', congregacao_id: congregacaoId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success' && response.data.length > 0) {
                                let options = '<option value="">Selecione a Classe</option>';
                                response.data.forEach(function(classe) {
                                    options += `<option value="${classe.id}">${classe.nome}</option>`;
                                });
                                $("#classe").html(options).prop('disabled', false);
                            } else {
                                $("#classe").html('<option value="">Nenhuma classe disponível</option>').prop('disabled', true);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Erro ao carregar as classes:", textStatus, errorThrown);
                            $("#classe").html('<option value="">Erro ao carregar as classes</option>').prop('disabled', true);
                        }
                    });
                } else {
                    $("#classe").prop('disabled', true).html('<option value="">Selecione a Classe</option>');
                }
            });

// Enviar dados e gerar relatório
$('#formRelatorio').on('submit', function(e) {
    e.preventDefault();

    const congregacao = $('#congregacao').val();
    const classe = $('#classe').val();
    const data_inicio = $('#data_inicio').val();
    const data_fim = $('#data_fim').val();

    $.ajax({
        url: '../../controllers/relatorio.php',
        method: 'GET',
        data: {
            congregacao: congregacao,
            classe: classe,
            data_inicio: data_inicio,
            data_fim: data_fim
        },
        success: function(data) {
            const resultados = JSON.parse(data);
            
            // Verificar se os resultados estão no formato esperado
            if (resultados.status && resultados.status === 'error') {
                alert(resultados.message);
                return;
            }

            renderizarTabela(resultados);
            renderizarGrafico(resultados);
        }
    });
});


// Função para renderizar a tabela
function renderizarTabela(dados) {
    const tbody = $('#tabelaRelatorio tbody');
    tbody.empty();

    // Verificar se 'total_frequencia' existe e é um array válido
    if (Array.isArray(dados.total_frequencia) && dados.total_frequencia.length > 0) {
        dados.total_frequencia.forEach(function(item) {
            const row = `<tr>
                <td>${item.data}</td>
                <td>${item.alunos_presentes}</td>
                <td>${item.alunos_ausentes}</td>
                <td>${item.visitantes}</td>
                <td>${parseInt(item.alunos_presentes) + parseInt(item.alunos_ausentes) + parseInt(item.visitantes)}</td>
                <td>${item.biblias}</td>
                <td>${item.revistas}</td>
                <td>${item.ofertas}</td>
            </tr>`;
            tbody.append(row);
        });
    } else {
        // Caso não haja dados, mostrar uma mensagem
        tbody.append('<tr><td colspan="8">Nenhum dado encontrado.</td></tr>');
    }

    $('#tabelaRelatorio').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        destroy: true
    });
}

// Função para renderizar o gráfico
function renderizarGrafico(dados) {
    const ctx = document.getElementById('graficoFrequencia').getContext('2d');
    const labels = dados.total_frequencia.map(item => item.data);
    const presentes = dados.total_frequencia.map(item => item.alunos_presentes);
    const ausentes = dados.total_frequencia.map(item => item.alunos_ausentes);
    const visitantes = dados.total_frequencia.map(item => item.visitantes);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Alunos Presentes',
                    data: presentes,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Alunos Ausentes',
                    data: ausentes,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Visitantes',
                    data: visitantes,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

            // Inicializar carregando as congregações
            carregarCongregacoes();
        });
    </script>
</body>
</html>

