<?php
session_start();

// Proteção da página: impede acessos diretos sem autenticação válida
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../loginADM.html");
    exit();
}

// Inclusão da conexão com o banco (Voltando duas pastas para achar a raiz onde fica o config.php)
include_once('../../config.php');

// ==========================================================================
// PROCESSAMENTO: ATUALIZAÇÃO REQUISITADA VIA FORMULÁRIO (Mudar estoque)
// ==========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_estoque'])) {
    $id_produto = (int)$_POST['id_produto'];
    $novo_estoque = (int)$_POST['quantidade_estoque'];

    // Atualiza a coluna 'estoque' usando o 'id' do produto
    $sql_update = "UPDATE produtos SET estoque = $novo_estoque WHERE id = $id_produto";
    if (mysqli_query($conexao, $sql_update)) {
        header("Location: produto.php?status=sucesso");
    } else {
        header("Location: produto.php?status=erro");
    }
    exit();
}

// ==========================================================================
// CONFIGURAÇÃO DA PAGINAÇÃO (Mostrar até 10 produtos por tela)
// ==========================================================================
$limite = 10; 
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $limite;

// Query para contar o total de produtos cadastrados
$total_produtos = 0;
$query_total = "SELECT COUNT(id) AS total FROM produtos";
if ($res_total = mysqli_query($conexao, $query_total)) {
    $row_total = mysqli_fetch_assoc($res_total);
    $total_produtos = (int)$row_total['total'];
}
$total_paginas = ceil($total_produtos / $limite);

// QUERY: Busca os registros de produtos respeitando a paginação
$query_produtos = "SELECT id, nome, preco, estoque FROM produtos ORDER BY id DESC LIMIT $limite OFFSET $offset";

try {
    $resultado_produtos = mysqli_query($conexao, $query_produtos);
    $erro_tabela = false;
} catch (mysqli_sql_exception $e) {
    $resultado_produtos = false;
    $erro_tabela = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Produtos & Estoque</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Css/indexADM.css">
</head>
<body class="oliver-painel font-sans bg-zinc-950 text-white">

    <div class="flex h-screen overflow-hidden">
       
        <aside class="w-64 bg-zinc-900 text-white flex-shrink-0 hidden md:flex flex-col border-r border-amber-500/25">
            <div class="p-6 text-2xl font-bold text-amber-500 flex items-center gap-2 border-b border-amber-500/10">
                Oliver'<span>ADMIN</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="../indexADM.php" class="flex items-center py-2.5 px-4 rounded hover:bg-zinc-800 transition-colors">
                    <i class="fas fa-users mr-3 text-amber-500"></i> Usuários
                </a>
                <a href="servicos.html" class="flex items-center py-2.5 px-4 rounded hover:bg-zinc-800 transition-colors">
                   <i class="fas fa-tools mr-3 text-amber-500"></i> Serviços
                </a>
                <a href="produto.php" class="flex items-center py-2.5 px-4 rounded bg-amber-500 text-black font-bold">
                    <i class="fas fa-box mr-3"></i> Produtos
                </a>
                <a href="orcamento.php" class="flex items-center py-2.5 px-4 rounded hover:bg-zinc-800 transition-colors">
                    <i class="fas fa-chart-line mr-3 text-amber-500"></i> Orçamento
                </a>
                <a href="pedidos.php" class="flex items-center py-2.5 px-4 rounded hover:bg-zinc-800 transition-colors">
                    <i class="fas fa-shopping-basket mr-3 text-amber-500"></i> Pedidos
                </a>
            </nav>
            <div class="p-4 border-t border-zinc-800">
                <a href="../../Php/logout.php" class="w-full flex items-center py-2 px-4 text-red-400 hover:bg-red-950/20 rounded transition-all">
                    <i class="fas fa-sign-out-alt mr-3"></i> Sair
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-y-auto bg-zinc-900">
           
            <header class="bg-zinc-950 border-b border-amber-500/25 py-4 px-8 flex justify-between items-center">
                <h1 class="text-xl font-semibold tracking-wide text-zinc-100">Controle de Estoque</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-zinc-400">Olá, <strong class="text-amber-500"><?php echo htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin'); ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=f59e0b&color=000000" class="w-10 h-10 rounded-full border border-amber-500/50" alt="Avatar">
                </div>
            </header>

            <div class="p-8">
               
                <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
                    <div class="mb-4 p-4 bg-green-900/40 border border-green-500/50 text-green-300 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Quantidade em estoque atualizada com sucesso!
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['status']) && $_GET['status'] == 'erro'): ?>
                    <div class="mb-4 p-4 bg-red-900/40 border border-red-500/50 text-red-300 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> Erro ao tentar alterar o estoque no sistema.
                    </div>
                <?php endif; ?>

                <div class="rounded-xl overflow-hidden shadow-2xl border border-zinc-800 bg-zinc-950">
                    <div class="p-6 flex justify-between items-center bg-zinc-900 border-b border-amber-500/20">
                        <h2 class="font-bold text-zinc-100 tracking-wide">Inventário de Acessórios</h2>
                        <span class="text-xs text-amber-500 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 font-medium">Total: <?php echo $total_produtos; ?> Itens</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-800 text-zinc-200 text-xs uppercase font-semibold border-b border-zinc-700">
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Produto</th>
                                    <th class="px-6 py-4">Preço</th>
                                    <th class="px-6 py-4">Qtd Restante</th>
                                    <th class="px-6 py-4 text-center">Situação do Estoque</th>
                                    <th class="px-6 py-4 text-center">Ações Rápidas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800 text-sm bg-zinc-900">
                                <?php
                                if ($erro_tabela) {
                                    echo '<tr><td colspan="6" class="px-6 py-8 text-center text-red-400 font-medium bg-zinc-900">
                                            <i class="fas fa-exclamation-triangle mr-2"></i> Erro ao carregar os produtos do banco de dados.<br>
                                          </td></tr>';
                                } elseif ($resultado_produtos && mysqli_num_rows($resultado_produtos) > 0) {
                                    while ($produto = mysqli_fetch_assoc($resultado_produtos)) {
                                        $estoque = (int)$produto['estoque'];
                                        
                                        // Regra visual de Alerta de Estoque
                                        if ($estoque === 0) {
                                            $badge_color = "bg-red-950 border-red-700 text-red-400";
                                            $badge_text = "❌ ESGOTADO / ZERADO";
                                        } elseif ($estoque <= 3) {
                                            $badge_color = "bg-amber-950 border-amber-700 text-amber-400";
                                            $badge_text = "⚠️ CRÍTICO (Restam apenas " . $estoque . ")";
                                        } else {
                                            $badge_color = "bg-green-950 border-green-700 text-green-400";
                                            $badge_text = "✅ Seguro (" . $estoque . " unidades)";
                                        }
                                        ?>
                                        <tr class="hover:bg-zinc-800/50 transition-colors border-b border-zinc-800">
                                            <td class="px-6 py-4 text-amber-500 font-bold">#<?php echo $produto['id']; ?></td>
                                            <td class="px-6 py-4 font-semibold text-zinc-100"><?php echo htmlspecialchars($produto['nome']); ?></td>
                                            <td class="px-6 py-4 text-zinc-300">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                            <td class="px-6 py-4 font-bold text-zinc-100"><?php echo $estoque; ?> un.</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="border px-2.5 py-1 rounded text-xs font-semibold <?php echo $badge_color; ?>">
                                                    <?php echo $badge_text; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <form method="POST" action="produto.php" class="flex items-center justify-center gap-2">
                                                    <input type="hidden" name="id_produto" value="<?php echo $produto['id']; ?>">
                                                    <input type="number" name="quantidade_estoque" value="<?php echo $estoque; ?>" min="0" class="w-16 text-center border border-zinc-700 rounded px-1.5 py-1 text-sm bg-zinc-950 text-white font-semibold focus:outline-none focus:border-amber-500">
                                                    <button type="submit" name="atualizar_estoque" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded p-1.5 transition-colors" title="Salvar Estoque">
                                                        <i class="fas fa-save text-xs"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="px-6 py-8 text-center text-zinc-500 bg-zinc-900">Nenhum acessório encontrado no banco de dados. Certifique-se de popular a tabela.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="px-6 py-4 bg-zinc-950 border-t border-zinc-800 flex items-center justify-between">
                            <div class="text-xs text-zinc-400 font-medium">
                                Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?> (Total: <?php echo $total_produtos; ?> produtos)
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($pagina_atual > 1): ?>
                                    <a href="produto.php?pagina=<?php echo $pagina_atual - 1; ?>" class="px-3 py-1.5 text-xs font-semibold rounded bg-zinc-800 text-zinc-300 hover:bg-amber-500 hover:text-black transition-colors">
                                        <i class="fas fa-chevron-left mr-1"></i> Anterior
                                    </a>
                                <?php endif; ?>

                                <?php if ($pagina_atual < $total_paginas): ?>
                                    <a href="produto.php?pagina=<?php echo $pagina_atual + 1; ?>" class="px-3 py-1.5 text-xs font-semibold rounded bg-zinc-800 text-amber-500 hover:bg-amber-500 hover:text-black transition-colors">
                                        Próxima <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

</body>
</html>