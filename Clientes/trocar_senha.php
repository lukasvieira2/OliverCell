<?php
session_start();

// Proteção: Garante que apenas usuários logados acessem a alteração de senha
if (!isset($_SESSION['usuario_email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oliver'CelL - Nova Senha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/perfil.css"> 
</head>
<body style="background-color: #000; font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

    <div style="border: 2px solid #ffcc00; padding: 40px; text-align: center; max-width: 400px; width: 100%; border-radius: 8px; background: #050505; box-sizing: border-box;">
        <h2 style="color: #ffcc00; text-transform: uppercase; margin-top: 0; letter-spacing: 1px;">Nova Senha</h2>
        <p style="color: #ccc; font-size: 14px; margin-bottom: 20px; line-height: 1.4;">Digite sua nova senha abaixo para atualizar no sistema de forma segura.</p>
        
        <?php if (isset($_GET['erro'])): ?>
            <div style="background: #ff4444; color: #fff; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 0.9rem; font-weight: bold;">
                Não foi possível atualizar. Tente novamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'senha_alterada'): ?>
            <div style="background: #00c851; color: #fff; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 0.9rem; font-weight: bold;">
                Senha atualizada com sucesso!
            </div>
        <?php endif; ?>

        <form action="../Php/processar_senha.php" method="POST">
            <label style="color: #fff; display: block; text-align: left; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">NOVA SENHA</label>
            <input type="password" name="nova_senha" required placeholder="Mínimo 6 caracteres"
                   style="width: 100%; padding: 12px; margin: 10px 0 20px 0; background: #111; border: 1px solid #333; color: #fff; border-radius: 4px; font-family: inherit; box-sizing: border-box; outline: none; transition: 0.3s;"
                   onfocus="this style.borderColor='#ffcc00'">
            
            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #ffcc00; border: none; font-weight: bold; color: #000; cursor: pointer; border-radius: 4px; text-transform: uppercase; font-size: 0.95rem; font-family: inherit; transition: 0.3s;"
                    onmouseover="this.style.backgroundColor='#fff'" 
                    onmouseout="this.style.backgroundColor='#ffcc00'">
                ATUALIZAR NO BANCO
            </button>
        </form>
        
        <a href="perfil.php" style="color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-top: 25px; font-size: 14px; transition: 0.3s;"
           onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='#fff'">
            <i class="fas fa-arrow-left"></i> Voltar ao Perfil
        </a>
    </div>

</body>
</html>