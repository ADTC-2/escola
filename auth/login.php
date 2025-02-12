<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <form id="formLogin">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Senha</label>
                            <input type="password" id="senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        <div id="msgErro" class="text-danger mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery.min.js"></script>
<script>
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
                    window.location.href = "../views/dashboard.php";
                } else {
                    $("#msgErro").text(res);
                }
            }
        });
    });
});
</script>
</body>
</html>
