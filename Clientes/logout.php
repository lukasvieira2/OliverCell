<?php
session_start();

// Remove todas as variáveis de sessão
session_unset();

// Destrói a sessão ativa
session_destroy();

// CORREÇÃO: Redireciona para o login.php correto na mesma pasta (Clientes/)
header("Location: login.php");
exit();
?>