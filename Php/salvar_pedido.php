<?php
header('Content-Type: application/json');
include_once('../config.php');

// Recebe os dados enviados via JSON pelo JavaScript do carrinho
$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados não recebidos de forma válida.']);
    exit();
}

$cliente_id        = (int)$dados['cliente_id'];
$carrinho          = $dados['carrinho'];
$forma_pagamento   = mysqli_real_escape_string($conexao, $dados['forma_pagamento']);
$nome_cliente      = mysqli_real_escape_string($conexao, $dados['nome_cliente']);
$sobrenome_cliente = mysqli_real_escape_string($conexao, $dados['sobrenome_cliente']);

if (empty($carrinho)) {
    echo json_encode(['sucesso' => false, 'erro' => 'O carrinho está vazio.']);
    exit();
}

// 1. Calcula o valor total calculando os itens vindos do localStorage
$valor_total = 0;
foreach ($carrinho as $item) {
    $valor_total += ($item['preco'] * $item['qtd']);
}

// 2. Insere o registro principal na tabela 'pedidos'
$query_pedido = "INSERT INTO pedidos (cliente_id, valor_total, forma_pagamento, status) 
                 VALUES ($cliente_id, $valor_total, '$forma_pagamento', 'Pendente')";

if (mysqli_query($conexao, $query_pedido)) {
    $pedido_id = mysqli_insert_id($conexao); // Captura o ID gerado para este pedido

    // 3. Varre o carrinho para inserir cada produto na tabela 'itens_pedido'
    foreach ($carrinho as $item) {
        $produto_nome  = mysqli_real_escape_string($conexao, $item['nome']);
        $quantidade    = (int)$item['qtd'];
        $preco_unitario = (float)$item['preco'];

        // AQUI ESTÁ O SEGREDO: Inserindo o nome e sobrenome na tabela de itens
        $query_item = "INSERT INTO itens_pedido (pedido_id, produto_nome, quantidade, preco_unitario, nome_cliente, sobrenome_cliente) 
                       VALUES ($pedido_id, '$produto_nome', $quantidade, $preco_unitario, '$nome_cliente', '$sobrenome_cliente')";
        
        mysqli_query($conexao, $query_item);
    }

    // Retorna a resposta de sucesso para o JavaScript abrir o WhatsApp
    echo json_encode(['sucesso' => true, 'pedido_id' => $pedido_id]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao salvar o pedido principal: ' . mysqli_error($conexao)]);
}
?>