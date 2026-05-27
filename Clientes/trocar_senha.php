<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Oliver'CelL - Nova Senha</title>
    <link rel="stylesheet" href="../Css/perfil.css"> </head>
<body style="background-color: #000; display: flex; justify-content: center; align-items: center; height: 100vh;">

    <div style="border: 2px solid #ffcc00; padding: 40px; text-align: center; max-width: 400px; width: 100%;">
        <h2 style="color: #ffcc00; text-transform: uppercase;">Nova Senha</h2>
        <p style="color: #ccc; font-size: 14px; margin-bottom: 20px;">Digite sua nova senha abaixo para atualizar no sistema.</p>
        
        <form action="../Php/processar_senha.php" method="POST">
            <label style="color: #fff; display: block; text-align: left; font-size: 12px;">NOVA SENHA</label>
            <input type="password" name="nova_senha" required 
                   style="width: 100%; padding: 10px; margin: 10px 0; background: #111; border: 1px solid #ffcc00; color: #fff;">
            
            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #ffcc00; border: none; font-weight: bold; cursor: pointer;">
                ATUALIZAR NO BANCO
            </button>
        </form>
        
        <a href="Perfil.php" style="color: #fff; text-decoration: none; display: block; margin-top: 20px; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Voltar ao Perfil
        </a>
    </div>

</body>
</html>