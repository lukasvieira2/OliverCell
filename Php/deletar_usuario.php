<?php
session_start();

// Verifica se o administrador está logado (se não estiver, volta para a tela de login)
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Administracao/loginADM.html");
    exit();
}

// Verifica se recebeu o ID válido para exclusão
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // O config.php está na raiz, no mesmo nível da pasta Php, então acessamos diretamente se estiver no mesmo escopo ou voltando um nível se necessário.
    // Com base na sua árvore: config.php está na raiz do projeto. O deletar_usuario.php está em /Php/. Para achar o config.php, usamos '../config.php'
    include_once('../config.php');
    
    $id = (int)$_GET['id']; // Segurança: garante que o ID seja um número inteiro
    
    // Query de remoção
    $query_delete = "DELETE FROM usuarios WHERE id = $id";
    
    if (mysqli_query($conexao, $query_delete)) {
        // CORREÇÃO AQUI: Volta para a pasta Administracao/indexADM.php
        header("Location: ../Administracao/indexADM.php?status=sucesso");
        exit();
    } else {
        // CORREÇÃO AQUI: Volta para a pasta Administracao/indexADM.php com status de erro
        header("Location: ../Administracao/indexADM.php?status=erro");
        exit();
    }
    
} else {
    // Se tentarem acessar a página sem passar um ID, apenas retorna ao painel
    header("Location: ../Administracao/indexADM.php");
    exit();
}
?>