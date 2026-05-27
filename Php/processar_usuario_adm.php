<?php
session_start();

// Usa o caminho real do sistema para ler o arquivo na raiz
$pathConfig = dirname(__DIR__) . '/config.php';
if (file_exists($pathConfig)) {
    include_once($pathConfig);
} else {
    die("Erro crítico: O arquivo config.php não foi localizado na raiz do projeto.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($conexao) || !$conexao) {
        die("Erro: A conexão com o banco de dados falhou. Verifique as credenciais no seu config.php.");
    }

    $nome  = mysqli_real_escape_string($conexao, trim($_POST['nome']));
    $email = mysqli_real_escape_string($conexao, trim($_POST['email']));
    $senha = trim($_POST['senha']); // Deixamos limpa aqui para criptografar abaixo
    
    if (empty($nome) || empty($email) || empty($senha)) {
        echo "<script>alert('Por favor, preencha todos os campos!'); window.history.back();</script>";
        exit();
    }

    // CRUCIAL: Criptografa a senha antes de salvar no banco!
    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);
    
    // Verifica se o e-mail de administrador já existe
    $checkEmail = mysqli_query($conexao, "SELECT id FROM administradores WHERE email = '$email'");
    
    if (mysqli_num_rows($checkEmail) > 0) {
        header("Location: ../Administracao/loginADM.html?erro=email_existe");
        exit();
    } else {
        // Insere o novo administrador com a senha criptografada de forma segura
        $sql = "INSERT INTO administradores (nome, email, senha) VALUES ('$nome', '$email', '$senha_criptografada')";
        
        if (mysqli_query($conexao, $sql)) {
            $novo_id = mysqli_insert_id($conexao);
            
            $_SESSION['admin_id'] = $novo_id;
            $_SESSION['admin_nome'] = $nome;
            
            // Redireciona com sucesso diretamente para o painel administrativo!
            header("Location: ../Administracao/indexADM.php");
            exit();
        } else {
            echo "Erro ao cadastrar no banco de dados: " . mysqli_error($conexao);
        }
    }
} else {
    header("Location: ../Administracao/loginADM.html");
    exit();
}
?>