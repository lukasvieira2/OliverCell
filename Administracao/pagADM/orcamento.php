<?php
session_start();

// Proteção da página: impede acessos diretos sem autenticação válida
if (!isset($_SESSION['admin_id'])) {
    // CORREÇÃO: Para sair de 'pagADM' e ir para a raiz onde está 'loginADM.html', usa-se apenas um '../'
    header("Location: ../loginADM.html");
    exit();
}

// Inclusão da conexão com o banco - Voltando duas pastas para achar a raiz
// CORREÇÃO: 'pagADM' está dentro de 'Administracao'. Para chegar na raiz onde está 'config.php', precisamos de ../../
include_once('../../config.php');

// ==========================================================================
// PROCESSAMENTO ASSÍNCRONO (AJAX): Salva a alteração do status no banco
// ==========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $id = (int)$_POST['id'];
    $novo_status = mysqli_real_escape_string($conexao, $_POST['status']);

    $query_update = "UPDATE orcamentos SET status = '$novo_status' WHERE id = $id";
    if (mysqli_query($conexao, $query_update)) {
        echo json_encode(['status' => 'sucesso']);
    } else {
        echo json_encode(['status' => 'erro', 'detalhes' => mysqli_error($conexao)]);
    }
    exit();
}

// ==========================================================================
// CONFIGURAÇÃO DA PAGINAÇÃO
// ==========================================================================
$limite = 10; 
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $limite;

// Conta o total de registros para a paginação funcionar
$total_orcamentos = 0;
$query_total = "SELECT COUNT(id) AS total FROM orcamentos";
if ($res_total = mysqli_query($conexao, $query_total)) {
    $row_total = mysqli_fetch_assoc($res_total);
    $total_orcamentos = (int)$row_total['total'];
}
$total_paginas = ceil($total_orcamentos / $limite);

// QUERY PRINCIPAL: Puxa os dados dos clientes
$query_orcamentos = "SELECT id, nome, modelo, defeito, data_solicitacao, status FROM orcamentos ORDER BY id DESC LIMIT $limite OFFSET $offset";

try {
    $resultado_orcamentos = mysqli_query($conexao, $query_orcamentos);
    $erro_tabela = false;
} catch (mysqli_sql_exception $e) {
    $resultado_orcamentos = false;
    $erro_tabela = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Orçamentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Css/indexADM.css">
</head>
<body class="oliver-painel font-sans">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-oliver-dark text-white flex-shrink-0 hidden md:flex flex-col border-r border-amber-500/25">
            <div class="p-6 text-2xl font-bold text-oliver-gold flex items-center gap-2 border-b border-amber-500/10">
                Oliver'<span>ADMIN</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="../indexADM.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-home mr-3"></i> Usuários
                </a>
                <a href="servicos.html" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                   <i class="fas fa-tools mr-3"></i> Serviços
                </a>
                <a href="produto.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-box mr-3"></i> Produtos
                </a>
                <a href="orcamento.php" class="flex items-center py-2.5 px-4 rounded nav-link-active">
                    <i class="fas fa-chart-line mr-3"></i> Orçamento
                </a>
                <a href="pedidos.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-shopping-bag mr-3"></i> Pedidos
                </a>
            </nav>
            <div class="p-4 border-t border-zinc-900">
                <a href="../../Php/logout.php" class="w-full flex items-center py-2 px-4 text-red-400 hover:bg-red-950/20 rounded transition-all">
                    <i class="fas fa-sign-out-alt mr-3"></i> Sair
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-y-auto">
            
            <header class="bg-oliver-dark border-b border-amber-500/25 py-4 px-8 flex justify-between items-center">
                <h1 class="text-xl font-semibold tracking-wide text-oliver-light">Visão Geral</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-oliver-muted">Olá, <strong class="text-oliver-gold"><?php echo htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin'); ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=f59e0b&color=000000" class="w-10 h-10 rounded-full border border-amber-500/50" alt="Avatar">
                </div>
            </header>

            <div class="p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Total de Orçamentos</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1"><?php echo $total_orcamentos; ?></div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-tools"></i> Solicitações via site</div>
                    </div>
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Faturamento Estimado</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1">R$ --.--</div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-wallet"></i> Caixa Oliver'CelL</div>
                    </div>
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Análises Pendentes</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1"><?php echo $total_orcamentos; ?></div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-file-invoice-dollar"></i> Aguardando retorno</div>
                    </div>
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Eficiência Técnica</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1">100%</div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-check-circle"></i> Rendimento</div>
                    </div>
                </div>

                <div class="rounded-xl overflow-hidden shadow-2xl border border-zinc-200 bg-white">
                    <div class="p-6 flex justify-between items-center bg-oliver-dark border-b border-amber-500/20">
                        <h2 class="font-bold text-oliver-light tracking-wide">Pedidos de Orçamentos Recebidos</h2>
                        <span class="text-xs text-amber-500 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 font-medium">Mostrando até 10 por página</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="table-header-oliver text-xs uppercase font-semibold">
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Cliente</th>
                                    <th class="px-6 py-4">Aparelho / Modelo</th>
                                    <th class="px-6 py-4">Defeito Solicitado (Pergunta)</th>
                                    <th class="px-6 py-4">Data de Envio</th>
                                    <th class="px-6 py-4 text-center">Status do Reparo</th>
                                </tr>
                            </thead>
                            <tbody class="table-body-white text-sm">
                                <?php
                                if ($erro_tabela) {
                                    echo '<tr><td colspan="6" class="px-6 py-8 text-center text-red-600 font-medium bg-white">
                                            <i class="fas fa-exclamation-triangle mr-2"></i> Erro ao carregar os dados do banco de dados.<br>
                                          </td></tr>';
                                } elseif ($resultado_orcamentos && mysqli_num_rows($resultado_orcamentos) > 0) {
                                    while ($orcamento = mysqli_fetch_assoc($resultado_orcamentos)) {
                                        $data_formatada = !empty($orcamento['data_solicitacao']) ? date('d/m/Y H:i', strtotime($orcamento['data_solicitacao'])) : 'Não informada';
                                        $status_atual = !empty($orcamento['status']) ? $orcamento['status'] : 'Pendente';
                                        ?>
                                        <tr class="table-row-oliver">
                                            <td class="px-6 py-4 text-id-gold font-bold">#<?php echo $orcamento['id']; ?></td>
                                            <td class="px-6 py-4 text-cliente-dark font-medium"><?php echo htmlspecialchars($orcamento['nome']); ?></td>
                                            <td class="px-6 py-4 text-zinc-700"><?php echo htmlspecialchars($orcamento['modelo']); ?></td>
                                            <td class="px-6 py-4 text-zinc-600 max-w-xs truncate" title="<?php echo htmlspecialchars($orcamento['defeito']); ?>">
                                                <?php echo htmlspecialchars($orcamento['defeito']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-zinc-600">
                                                <span class="bg-zinc-100 border border-zinc-200 text-zinc-700 px-2.5 py-1 rounded text-xs font-medium">
                                                    <i class="far fa-calendar-alt mr-1 text-zinc-500"></i> <?php echo $data_formatada; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <select onchange="atualizarStatusNoBanco(<?php echo $orcamento['id']; ?>, this)" class="rounded px-2.5 py-1 text-xs font-bold border cursor-pointer focus:outline-none shadow-sm transition-all
                                                    <?php 
                                                        if($status_atual == 'Pendente') echo 'bg-red-100 text-red-800 border-red-300';
                                                        elseif($status_atual == 'Em Andamento') echo 'bg-amber-100 text-amber-800 border-amber-300';
                                                        elseif($status_atual == 'Concluído') echo 'bg-green-100 text-green-800 border-green-300';
                                                    ?>">
                                                    <option value="Pendente" <?php if($status_atual == 'Pendente') echo 'selected'; ?>>🔴 Pendente</option>
                                                    <option value="Em Andamento" <?php if($status_atual == 'Em Andamento') echo 'selected'; ?>>🟡 Em Andamento</option>
                                                    <option value="Concluído" <?php if($status_atual == 'Concluído') echo 'selected'; ?>>🟢 Concluído</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="px-6 py-8 text-center text-zinc-400 bg-white">Nenhum pedido de orçamento encontrado.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="px-6 py-4 bg-zinc-50 border-t border-zinc-200 flex items-center justify-between">
                            <div class="text-xs text-zinc-500 font-medium">
                                Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?> (Total: <?php echo $total_orcamentos; ?> cadastros)
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($pagina_atual > 1): ?>
                                    <a href="orcamento.php?pagina=<?php echo $pagina_atual - 1; ?>" class="px-3 py-1.5 text-xs font-semibold rounded bg-zinc-200 text-zinc-700 hover:bg-amber-500 hover:text-black transition-colors">
                                        <i class="fas fa-chevron-left mr-1"></i> Anterior
                                    </a>
                                <?php endif; ?>

                                <?php if ($pagina_atual < $total_paginas): ?>
                                    <a href="orcamento.php?pagina=<?php echo $pagina_atual + 1; ?>" class="px-3 py-1.5 text-xs font-semibold rounded bg-oliver-dark text-oliver-gold hover:bg-amber-500 hover:text-black transition-colors">
                                        Próxima <i class="fas fa-chevron-right mr-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <script>
    function atualizarStatusNoBanco(idOrcamento, elementoSelect) {
        const valorSelecionado = elementoSelect.value;
        const dadosForm = new FormData();
        dadosForm.append('atualizar_status', '1');
        dadosForm.append('id', idOrcamento);
        dadosForm.append('status', valorSelecionado);

        fetch(window.location.href, {
            method: 'POST',
            body: dadosForm
        })
        .then(resposta => resposta.json())
        .then(dados => {
            if (dados.status === 'sucesso') {
                elementoSelect.className = "rounded px-2.5 py-1 text-xs font-bold border cursor-pointer focus:outline-none shadow-sm transition-all ";
                
                if(valorSelecionado === 'Pendente') {
                    elementoSelect.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
                } else if(valorSelecionado === 'Em Andamento') {
                    elementoSelect.classList.add('bg-amber-100', 'text-amber-800', 'border-amber-300');
                } else if(valorSelecionado === 'Concluído') {
                    elementoSelect.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
                }
            } else {
                alert('Ocorreu um erro ao salvar o status: ' + dados.detalhes);
            }
        })
        .catch(erro => {
            console.error('Erro:', erro);
            alert('Não foi possível conectar ao banco de dados.');
        });
    }
    </script>

</body>
</html>