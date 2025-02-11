<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Escola Bíblica</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .alert {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-container">
            <h3 class="text-center"><i class="fa-solid fa-user-lock"></i> Login</h3>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            ?>

            <form method="POST" action="../controllers/autenticacao.php">
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fa-solid fa-user"></i> Usuário</label>
                    <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
    
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label"><i class="fa-solid fa-lock"></i> Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-sign-in-alt"></i> Entrar</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>






