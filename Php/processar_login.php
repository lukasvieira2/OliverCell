<?php
session_start();
include_once('../config.php'); // Certifique-se de que a conexão está correta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $senha = $_POST['senha']; // Recebe a senha digitada

    // Busca o usuário apenas pelo e-mail informado
    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $query);

    // CRUCIAL: Verifica se encontrou EXATAMENTE 1 registro no banco de dados
    if ($resultado && mysqli_num_rows($resultado) === 1) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Altere para password_verify($senha, $usuario['senha']) se usar criptografia
        if ($senha === $usuario['senha']) {
            
            // Define as variáveis de sessão do cliente
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];

            // Redireciona para o perfil do cliente
            header("Location: ../Clientes/cliente.html");
            exit();
        } else {
            // Senha incorreta
            header("Location: ../Clientes/Login.php?erro=senha_incorreta");
            exit();
        }
    } else {
        // E-mail não encontrado ou excluído do banco
        header("Location: ../Clientes/Login.php?erro=usuario_nao_encontrado");
        exit();
    }
}