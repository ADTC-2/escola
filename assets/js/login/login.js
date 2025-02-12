$(document).ready(function() {
    $("#formLogin").submit(function(event) {
        event.preventDefault();
        var email = $("#email").val();
        var senha = $("#senha").val();

        $.ajax({
            url: "validar_login.php",
            type: "POST",
            data: { email: email, senha: senha },
            success: function(res) {
                if (res == "sucesso") {
                    window.location.href = "../views/dashboard.php";  // Redireciona para a dashboard
                } else {
                    $("#msgErro").text(res);  // Exibe a mensagem de erro
                }
            }
        });
    });
});
