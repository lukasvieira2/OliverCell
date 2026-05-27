<?php
session_start();
// Busca o config saindo da pasta Php
include_once(__DIR__ . '/../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se a conexão vinda do config.php existe
    if (!isset($conexao)) {
        die("Erro Crítico: Variável de conexão não definida.");
    }

    $nome  = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $senha = $_POST['senha']; 

    // 1. VERIFICAÇÃO DE DUPLICATA: Evita o erro fatal 'Duplicate entry'
    $checkEmail = "SELECT email FROM usuarios WHERE email = '$email'";
    $resCheck = mysqli_query($conexao, $checkEmail);

    if (mysqli_num_rows($resCheck) > 0) {
        echo "<script>
                alert('Este e-mail já está cadastrado em nosso sistema!');
                window.history.back();
              </script>";
        exit();
    }

    // 2. INSERÇÃO: Usando as colunas exatas da sua tabela
    $sql = "INSERT INTO usuarios (nome, email, senha, data_cadastro) 
            VALUES ('$nome', '$email', '$senha', NOW())";

    if (mysqli_query($conexao, $sql)) {
        echo "<script>
                alert('Cadastro da Oliver\'CelL realizado com sucesso!');
                window.location.href = '../Clientes/Login.php';
              </script>";
    } else {
        echo "Erro ao cadastrar: " . mysqli_error($conexao);
    }
}
?>