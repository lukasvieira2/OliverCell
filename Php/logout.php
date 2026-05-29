<?php
session_start();
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destrói a sessão de fato

// CORREÇÃO: Volta apenas uma pasta (../) para encontrar o index.php na raiz do OliverCell
header("Location: ../index.php");
exit();
?>