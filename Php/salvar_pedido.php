<?php
header('Content-Type: application/json');
session_start();

// Carrega a conexão com o banco de dados
$pathConfig = dirname(__DIR__) . '/config.php';
if (file_exists($pathConfig)) {
    include_once($pathConfig);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Arquivo config.php não localizado.']);
    exit();
}

// Lê o corpo da requisição JSON do JavaScript
$dadosRecebidos = json_decode(file_get_contents('php://input'), true);

if (!$dadosRecebidos || empty($dadosRecebidos['carrinho'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Carrinho vazio ou dados inválidos.']);
    exit();
}

$carrinho = $dadosRecebidos['carrinho'];
$forma_pagamento = mysqli_real_escape_string($conexao, $dadosRecebidos['forma_pagamento']);
$cliente_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Se houver login de cliente, vincula o ID

// Calcula o valor total geral
$valor_total = 0;
foreach ($carrinho as $item) {
    $preco = isset($item['preco']) ? (float)$item['preco'] : 0;
    $qtd = isset($item['qtd']) ? (int)$item['qtd'] : 1;
    $valor_total += ($preco * $qtd);
}

// Desativa o auto-commit para garantir transação segura (se falhar um item, cancela tudo)
mysqli_begin_transaction($conexao);

try {
    // 1. Insere o cabeçalho na tabela 'pedidos'
    $queryPedido = "INSERT INTO pedidos (cliente_id, valor_total, status, forma_pagamento) VALUES (?, ?, 'Pendente', ?)";
    $stmtPedido = mysqli_prepare($conexao, $queryPedido);
    mysqli_stmt_bind_param($stmtPedido, "ids", $cliente_id, $valor_total, $forma_pagamento);
    
    if (!mysqli_stmt_execute($stmtPedido)) {
        throw new Exception("Falha ao registrar o topo do pedido.");
    }
    
    // Obtém o ID do pedido gerado acima
    $pedido_id = mysqli_insert_id($conexao);
    mysqli_stmt_close($stmtPedido);

    // 2. Percorre o carrinho inserindo os produtos na tabela 'itens_pedido'
    $queryItem = "INSERT INTO itens_pedido (pedido_id, produto_nome, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
    $stmtItem = mysqli_prepare($conexao, $queryItem);

    foreach ($carrinho as $item) {
        $nome_produto = mysqli_real_escape_string($conexao, $item['nome']);
        $quantidade = (int)$item['qtd'];
        $preco_unitario = (float)$item['preco'];

        mysqli_stmt_bind_param($stmtItem, "isid", $pedido_id, $nome_produto, $quantidade, $preco_unitario);
        
        if (!mysqli_stmt_execute($stmtItem)) {
            throw new Exception("Erro ao inserir o item: " . $nome_produto);
        }
    }
    mysqli_stmt_close($stmtItem);

    // Confirma as alterações salvando definitivamente no banco
    mysqli_commit($conexao);

    // Devolve para o JavaScript o número do pedido gerado
    echo json_encode(['sucesso' => true, 'pedido_id' => $pedido_id]);

} catch (Exception $e) {
    // Cancela qualquer inserção caso ocorra erro no meio do processo
    mysqli_rollback($conexao);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>