<?php
session_start();

// 1. CORREÇÃO DO CAMINHO (Mantém como você fez, que está correto)
$config_path = __DIR__ . '/../config.php';

if (file_exists($config_path)) {
    include_once($config_path);
} else {
    die("Erro Crítico: O arquivo config.php não foi encontrado.");
}

// 2. VERIFICAÇÃO DE SEGURANÇA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_email'])) {
    
    // Garante que a conexão vinda do config.php (usando a porta 3307 do seu XAMPP) está ativa
    if (!isset($conexao) || !$conexao) {
        die("Erro: A conexão com o banco de dados falhou. Verifique a porta 3307 no seu config.php.");
    }

    $email = $_SESSION['usuario_email'];
    $novaSenha = $_POST['nova_senha']; 

    // 3. ATUALIZAÇÃO NO BANCO (Usando texto puro para evitar erro de login depois)
    // Se o seu login usar criptografia, volte para: password_hash($novaSenha, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET senha = '$novaSenha' WHERE email = '$email'";
    
    if (mysqli_query($conexao, $sql)) {
        echo "<script>
                alert('Senha atualizada com sucesso!');
                window.location.href='../Clientes/Perfil.php';
              </script>";
    } else {
        echo "Erro ao atualizar: " . mysqli_error($conexao);
    }
} else {
    header("Location: ../Login.php");
    exit();
}
?>