<?php
session_start();

// Proteção da página: impede acessos diretos sem autenticação válida
if (!isset($_SESSION['admin_id'])) {
    // CORREÇÃO: Para sair de 'pagADM' e ir para a raiz buscar 'loginADM.html', voltamos duas pastas
    header("Location: ../../loginADM.html");
    exit();
}

// Inclusão da conexão com o banco
include_once('../../config.php');

// ==========================================================================
// PROCESSAMENTO DAS ALTERAÇÕES DE STATUS (Confirmar ou Concluir)
// ==========================================================================
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id_pedido = (int)$_GET['id'];
    $acao = $_GET['acao'];
    $pagina_retorno = (int)($_GET['pagina'] ?? 1);

    // SEGURANÇA: Evita SQL Injection garantindo que as ações executadas sejam estritamente as esperadas
    if ($acao === 'confirmar') {
        $query_update = "UPDATE pedidos SET status = 'Confirmado' WHERE id = $id_pedido";
        mysqli_query($conexao, $query_update);
    } elseif ($acao === 'concluir') {
        $query_update = "UPDATE pedidos SET status = 'Entregue' WHERE id = $id_pedido";
        mysqli_query($conexao, $query_update);
    }

    header("Location: pedidos.php?pagina=" . $pagina_retorno);
    exit();
}

// ==========================================================================
// CONFIGURAÇÃO DA PAGINAÇÃO (Mostrar até 10 pedidos por tela)
// ==========================================================================
$limite = 10; 
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $limite;

// Query para contar o total de pedidos cadastrados
$total_pedidos = 0;
$query_total = "SELECT COUNT(id) AS total FROM pedidos";
if ($res_total = mysqli_query($conexao, $query_total)) {
    $row_total = mysqli_fetch_assoc($res_total);
    $total_pedidos = (int)$row_total['total'];
}
$total_paginas = ceil($total_pedidos / $limite);

// QUERY DOS PEDIDOS: Busca os dados puros da tabela pedidos
$query_pedidos = "SELECT * FROM pedidos ORDER BY id DESC LIMIT $limite OFFSET $offset";

try {
    $resultado_pedidos = mysqli_query($conexao, $query_pedidos);
    $erro_tabela = false;
} catch (mysqli_sql_exception $e) {
    $resultado_pedidos = false;
    $erro_tabela = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Pedidos dos Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Css/indexADM.css">
</head>
<body class="oliver-painel font-sans bg-zinc-900">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-oliver-dark text-white flex-shrink-0 hidden md:flex flex-col border-r border-amber-500/25">
            <div class="p-6 text-2xl font-bold text-oliver-gold flex items-center gap-2 border-b border-amber-500/10">
                Oliver'<span>ADMIN</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="../indexADM.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-user mr-3"></i> Usuários
                </a>
                <a href="servicos.html" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                   <i class="fas fa-tools mr-3"></i> Serviços
                </a>
                <a href="produto.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-box mr-3"></i> Produtos
                </a>
                <a href="orcamento.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-chart-line mr-3"></i> Orçamento
                </a>
                <a href="pedidos.php" class="flex items-center py-2.5 px-4 rounded nav-link-active bg-amber-500 text-black font-semibold">
                    <i class="fas fa-shopping-basket mr-3"></i> Pedidos
                </a>
            </nav>
            <div class="p-4 border-t border-zinc-900">
                <a href="../../Php/logout.php" class="w-full flex items-center py-2 px-4 text-red-400 hover:bg-red-950/20 rounded transition-all">
                    <i class="fas fa-sign-out-alt mr-3"></i> Sair
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-y-auto bg-zinc-900 text-white">
            
            <header class="bg-zinc-950 border-b border-amber-500/25 py-4 px-8 flex justify-between items-center text-white">
                <h1 class="text-xl font-semibold tracking-wide text-amber-500">Gerenciamento de Pedidos</h1>
                <div class="flex items-center space-x-4">
                    <span>Olá, <strong class="text-amber-500"><?php echo htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin'); ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=f59e0b&color=000000" class="w-10 h-10 rounded-full border border-amber-500/50" alt="Avatar">
                </div>
            </header>

            <div class="p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-amber-500 p-6 rounded-xl shadow-xl text-black">
                        <div class="text-xs font-bold uppercase tracking-wider opacity-75">Total de Pedidos Recebidos</div>
                        <div class="text-3xl font-extrabold mt-1"><?php echo $total_pedidos; ?></div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h2 class="text-lg font-semibold border-b border-zinc-800 pb-2 text-zinc-400">Últimos Pedidos</h2>

                    <?php if ($erro_tabela || !$resultado_pedidos || mysqli_num_rows($resultado_pedidos) === 0): ?>
                        <div class="bg-zinc-950 border border-zinc-800 text-zinc-400 p-6 rounded-lg text-center">
                            Nenhum pedido encontrado no sistema.
                        </div>
                    <?php else: ?>
                        <?php while ($pedido = mysqli_fetch_assoc($resultado_pedidos)): ?>
                            
                            <?php 
                            $id_pedido_atual = (int)$pedido['id'];
                            $query_itens = "SELECT * FROM itens_pedido WHERE pedido_id = $id_pedido_atual";
                            $resultado_itens = mysqli_query($conexao, $query_itens);
                            
                            $nome_cliente_detectado = 'Cliente não informado';
                            $itens_array = [];
                            
                            if ($resultado_itens && mysqli_num_rows($resultado_itens) > 0) {
                                while ($item_row = mysqli_fetch_assoc($resultado_itens)) {
                                    $itens_array[] = $item_row;
                                    if (!empty($item_row['nome_cliente'])) {
                                        $nome_cliente_detectado = trim($item_row['nome_cliente'] . ' ' . ($item_row['sobrenome_cliente'] ?? ''));
                                    }
                                }
                            }
                            ?>

                            <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-6 shadow-md hover:border-amber-500/40 transition-all">
                                
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-zinc-800 pb-4 mb-4 gap-4">
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-500">Pedido nº #<?php echo $pedido['id']; ?></span>
                                        
                                        <div class="text-base font-semibold text-white mt-1">
                                            <i class="fas fa-user text-amber-500 text-sm mr-1"></i> 
                                            <?php echo htmlspecialchars($nome_cliente_detectado); ?>
                                        </div>

                                        <div class="text-sm text-zinc-400 mt-1">
                                            Forma de Pagamento: <span class="text-white font-medium"><?php echo htmlspecialchars($pedido['forma_pagamento'] ?? 'Não informada'); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <?php 
                                        $status_atual = $pedido['status'] ?? 'Pendente';
                                        if ($status_atual === 'Pendente'): 
                                        ?>
                                            <span class="px-3 py-1 bg-amber-500/10 border border-amber-500 text-amber-400 text-xs font-bold rounded-full animate-pulse">
                                                <i class="fas fa-clock mr-1"></i> Pendente
                                            </span>
                                            <a href="pedidos.php?acao=confirmar&id=<?php echo $pedido['id']; ?>&pagina=<?php echo $pagina_atual; ?>" 
                                               class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-black text-xs font-bold rounded transition-all flex items-center gap-1"
                                               onclick="return confirm('Deseja marcar o Pedido #<?php echo $pedido['id']; ?> como Confirmado?')">
                                                <i class="fas fa-check"></i> Confirmar Pedido
                                            </a>

                                        <?php elseif ($status_atual === 'Confirmado'): ?>
                                            <span class="px-3 py-1 bg-blue-500/10 border border-blue-500 text-blue-400 text-xs font-bold rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i> Confirmado
                                            </span>
                                            <a href="pedidos.php?acao=concluir&id=<?php echo $pedido['id']; ?>&pagina=<?php echo $pagina_atual; ?>" 
                                               class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded transition-all flex items-center gap-1"
                                               onclick="return confirm('Confirmar entrega pessoal para o Pedido #<?php echo $pedido['id']; ?>? Ele sumirá do perfil do cliente.')">
                                                <i class="fas fa-handshake"></i> Concluir Entrega
                                            </a>

                                        <?php elseif ($status_atual === 'Entregue'): ?>
                                            <span class="px-3 py-1 bg-green-500/20 border border-green-500 text-green-400 text-xs font-bold rounded-full">
                                                <i class="fas fa-clipboard-check mr-1"></i> Entregue Pessoalmente
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="bg-zinc-900/60 rounded-lg p-4 border border-zinc-800/50 mb-4">
                                    <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Itens do Pedido:</div>
                                    <div class="space-y-2">
                                        <?php 
                                        if (count($itens_array) > 0): 
                                            foreach ($itens_array as $item):
                                                $subtotal = $item['quantidade'] * $item['preco_unitario'];
                                        ?>
                                                <div class="flex justify-between items-center text-sm border-b border-zinc-800/30 pb-2 last:border-0 last:pb-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-amber-500 font-bold"><?php echo $item['quantidade']; ?>x</span>
                                                        <span class="text-zinc-200"><?php echo htmlspecialchars($item['produto_nome']); ?></span>
                                                    </div>
                                                    <div class="text-xs text-zinc-400">
                                                        <span><?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?> cada</span>
                                                        <span class="ml-4 text-white font-semibold">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                                                    </div>
                                                </div>
                                        <?php 
                                            endforeach;
                                        else: 
                                        ?>
                                            <span class="text-xs text-red-400">Nenhum produto listado para este pedido.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="text-right text-sm">
                                    <span class="text-zinc-400">Valor Total do Pedido:</span>
                                    <strong class="text-lg text-green-400 ml-2">R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></strong>
                                </div>

                             </div>
                        <?php endwhile; ?>
                    <?php endif; ?>

                </div>

                <?php if ($total_paginas > 1): ?>
                    <div class="flex justify-center items-center mt-8 space-x-2">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <a href="pedidos.php?pagina=<?php echo $i; ?>" 
                               class="px-4 py-2 rounded font-bold transition-all text-sm <?php echo $i === $pagina_atual ? 'bg-amber-500 text-black' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>
</html>