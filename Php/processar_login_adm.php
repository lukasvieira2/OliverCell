<?php
session_start();
$pathConfig = dirname(__DIR__) . '/config.php';
if (file_exists($pathConfig)) {
    include_once($pathConfig);
} else {
    die("Erro crítico: O arquivo config.php não foi localizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conexao, trim($_POST['email']));
    $senha = trim($_POST['senha']);

    $sql = "SELECT id, nome, senha FROM administradores WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conexao, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);

        // Verifica a senha criptografada usando password_verify
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['admin_id'] = $usuario['id'];
            $_SESSION['admin_nome'] = $usuario['nome'];

            // Redireciona corretamente voltando uma pasta e entrando na Administração
            header("Location: ../Administracao/indexADM.php");
            exit();
        } else {
            header("Location: ../Administracao/loginADM.html?erro=senha_incorreta");
            exit();
        }
    } else {
        header("Location: ../Administracao/loginADM.html?erro=usuario_inexistente");
        exit();
    }
} else {
    header("Location: ../Administracao/loginADM.html");
    exit();
}
?>