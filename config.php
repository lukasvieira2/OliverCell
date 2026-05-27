<?php
// Configurações de conexão para o Oliver'CelL
$host = '127.0.0.1'; // IP fixo evita problemas de porta
$user = 'root';
$senha = ''; 
$banco = 'olivercell';
$port  = 3307; // Porta que aparece no seu XAMPP

// Criando a conexão com a variável correta: $conexao
$conexao = mysqli_connect($host, $user, $senha, $banco, $port);

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>