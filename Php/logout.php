<?php
session_start();
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destrói a sessão

// O segredo está aqui: ../ volta uma pasta para achar a index.php na raiz
header("Location: ../../index.php");
exit();
?>