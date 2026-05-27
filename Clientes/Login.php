<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oliver'CelL - Acesso</title>
    <link rel="icon" href="../imagens/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/login.css">
</head>
<body>

<header class="header">
    <div class="header-container">
        <a href="../index.php" class="logo-link">
            <img src="../imagens/logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">Oliver'<span>CelL</span></span>
        </a>
    </div>
</header>

<main class="main-content">
    <div class="auth-container">
        
        <div class="info-side">
            <div id="info-login">
                <h3>Não tem login?</h3>
                <p>Crie sua conta agora para acompanhar seus orçamentos e serviços em tempo real.</p>
                <button class="toggle-btn" onclick="toggleForm('signup')">CADASTRE-SE AQUI</button>
            </div>
            
            <div id="info-signup" class="hidden">
                <h3>Já é cliente?</h3>
                <p>Acesse sua conta para verificar o status do seu aparelho.</p>
                <button class="toggle-btn" onclick="toggleForm('login')">FAZER LOGIN</button>
            </div>
        </div>

        <div class="form-side">
            <div id="login-section">
                <h2 class="form-title">Entrar</h2>
                <p class="form-subtitle">ÁREA DO CLIENTE</p>
                <form action="../Php/processar_login.php" method="POST">
                    <input type="email" name="email" class="form-input" placeholder="E-mail" required>
                    <input type="password" name="senha" class="form-input" placeholder="Senha" required>
                    
                    <div class="btn-group">
                        <button type="submit" class="auth-btn">ACESSAR SISTEMA</button>
                        <button type="button" onclick="window.location.href='../index.php'" class="auth-btn btn-cancel">CANCELAR</button>
                    </div>
                </form>
            </div>

            <div id="signup-section" class="hidden">
                <h2 class="form-title">Cadastro</h2>
                <p class="form-subtitle">CRIE SUA CONTA</p>
                <form action="../Php/processar_usuario.php" method="POST">
                    <input type="text" name="nome" class="form-input" placeholder="Nome Completo" required>
                    <input type="email" name="email" class="form-input" placeholder="E-mail" required>
                    <input type="password" name="senha" class="form-input" placeholder="Senha" required>
                    
                    <div class="btn-group">
                        <button type="submit" class="auth-btn">FINALIZAR CONTA</button>
                        <button type="button" onclick="toggleForm('login')" class="auth-btn btn-cancel">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<footer>
    <p>&copy; 2026 Oliver'CelL. Todos os direitos reservados.</p>
</footer>

<script>
   function toggleForm(mode) {
    const loginSection = document.getElementById('login-section');
    const signupSection = document.getElementById('signup-section');
    const infoLogin = document.getElementById('info-login');
    const infoSignup = document.getElementById('info-signup');

    if (mode === 'signup') {
        loginSection.classList.add('hidden');
        signupSection.classList.remove('hidden');
        infoLogin.classList.add('hidden');
        infoSignup.classList.remove('hidden');
    } else {
        signupSection.classList.add('hidden');
        loginSection.classList.remove('hidden');
        infoSignup.classList.add('hidden');
        infoLogin.classList.remove('hidden');
    }
}
</script>

</body>
</html>