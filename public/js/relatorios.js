$(document).ready(function() {
    $.get("../controllers/relatorios.php", function(data) {
        let dados = JSON.parse(data);

        // Preenche a tabela de frequência
        let html = "";
        dados.frequencia.forEach(aluno => {
            html += `<tr>
                        <td>${aluno.nome}</td>
                        <td>${aluno.classe}</td>
                        <td>${aluno.presencas}</td>
                        <td>${aluno.faltas}</td>
                    </tr>`;
        });
        $("#tabela-frequencia").html(html);

        // Exibe quem mais faltou
        if (dados.quemMaisFaltou) {
            $("#quemMaisFaltou").text(`${dados.quemMaisFaltou.nome} (${dados.quemMaisFaltou.classe}) com ${dados.quemMaisFaltou.total_faltas} faltas.`);
        } else {
            $("#quemMaisFaltou").text("Nenhum dado disponível.");
        }

        // Exibe quem mais compareceu
        if (dados.quemMaisCompareceu) {
            $("#quemMaisCompareceu").text(`${dados.quemMaisCompareceu.nome} (${dados.quemMaisCompareceu.classe}) com ${dados.quemMaisCompareceu.total_presencas} presenças.`);
        } else {
            $("#quemMaisCompareceu").text("Nenhum dado disponível.");
        }
    });
});
