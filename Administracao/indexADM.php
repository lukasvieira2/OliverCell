<?php
session_start();

// Proteção da página: impede acessos diretos sem autenticação válida
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginADM.html");
    exit();
}

// Inclusão da conexão com o banco (Voltando um nível para achar a raiz onde fica o config.php)
include_once('../config.php');

// ==========================================================================
// CONFIGURAÇÃO DA PAGINAÇÃO (Mostrar até 10 usuários por tela)
// ==========================================================================
$limite = 10; 
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $limite;

// Query para contar o total de usuários cadastrados
$total_usuarios = 0;
$query_total = "SELECT COUNT(id) AS total FROM usuarios";
if ($res_total = mysqli_query($conexao, $query_total)) {
    $row_total = mysqli_fetch_assoc($res_total);
    $total_usuarios = (int)$row_total['total'];
}
$total_paginas = ceil($total_usuarios / $limite);

// QUERY: Busca até 10 registros respeitando a paginação
$query_usuarios = "SELECT id, nome, email, data_cadastro FROM usuarios ORDER BY id DESC LIMIT $limite OFFSET $offset";

try {
    $resultado_usuarios = mysqli_query($conexao, $query_usuarios);
    $erro_tabela = false;
} catch (mysqli_sql_exception $e) {
    $resultado_usuarios = false;
    $erro_tabela = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/indexADM.css">
</head>
<body class="oliver-painel font-sans">

    <div class="flex h-screen overflow-hidden">
       
        <aside class="w-64 bg-oliver-dark text-white flex-shrink-0 hidden md:flex flex-col border-r border-amber-500/25">
            <div class="p-6 text-2xl font-bold text-oliver-gold flex items-center gap-2 border-b border-amber-500/10">
                Oliver'<span>ADMIN</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="#" class="flex items-center py-2.5 px-4 rounded nav-link-active">
                    <i class="fas fa-user mr-3"></i> Usuários
                </a>
                <a href="pagADM/servicos.html" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                   <i class="fas fa-tools mr-3"></i> Serviços
                </a>
                <a href="pagADM/produto.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-box mr-3"></i> Produtos
                </a>
                <a href="pagADM/orcamento.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-chart-line mr-3"></i> Orçamento
                </a>
                <a href="pagADM/pedidos.php" class="flex items-center py-2.5 px-4 rounded nav-link-oliver">
                    <i class="fas fa-shopping-basket mr-3"></i> Pedidos
                </a>
            </nav>
            <div class="p-4 border-t border-zinc-900">
                <a href="../Php/logout.php" class="w-full flex items-center py-2 px-4 text-red-400 hover:bg-red-950/20 rounded transition-all">
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
               
                <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
                    <div class="mb-4 p-4 bg-green-900/40 border border-green-500/50 text-green-300 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Usuário removido com sucesso!
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['status']) && $_GET['status'] == 'erro'): ?>
                    <div class="mb-4 p-4 bg-red-900/40 border border-red-500/50 text-red-300 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> Erro ao tentar excluir o usuário.
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Pessoas Cadastradas</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1"><?php echo $total_usuarios; ?></div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-users"></i> Usuários ativos</div>
                    </div>
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Lucro Realizado</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1">R$ 24.500</div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-wallet"></i> Caixa Oliver'CelL</div>
                    </div>
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Pedidos Pendentes</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1">12</div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-file-invoice-dollar"></i> Aguardando análise</div>
                    </div>
                    <div class="bg-oliver-card-gold p-6 rounded-xl shadow-xl border-oliver-gold">
                        <div class="text-oliver-title-gold-dark text-xs font-bold uppercase tracking-wider">Taxa de Conversão</div>
                        <div class="text-3xl font-extrabold text-oliver-number-dark mt-1">3.4%</div>
                        <div class="text-amber-950 text-sm mt-2 flex items-center gap-1"><i class="fas fa-check-circle"></i> Rendimento</div>
                    </div>
                </div>

                <div class="rounded-xl overflow-hidden shadow-2xl border border-zinc-200 bg-white">
                    <div class="p-6 flex justify-between items-center bg-oliver-dark border-b border-amber-500/20">
                        <h2 class="font-bold text-oliver-light tracking-wide">Últimos Usuários Cadastrados</h2>
                        <span class="text-xs text-amber-500 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 font-medium">Mostrando 10 por página</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="table-header-oliver text-xs uppercase font-semibold">
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Nome</th>
                                    <th class="px-6 py-4">E-mail</th>
                                    <th class="px-6 py-4">Data de Cadastro</th>
                                    <th class="px-6 py-4 text-center">Ações</th>
                                </tr>
                            </table>
                            </thead>
                            <tbody class="table-body-white text-sm">
                                <?php
                                if ($erro_tabela) {
                                    echo '<tr><td colspan="5" class="px-6 py-8 text-center text-red-600 font-medium bg-white">
                                            <i class="fas fa-exclamation-triangle mr-2"></i> Erro ao carregar os dados do banco.<br>
                                          </td></tr>';
                                } elseif ($resultado_usuarios && mysqli_num_rows($resultado_usuarios) > 0) {
                                    while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) {
                                        $data_formatada = !empty($usuario['data_cadastro']) ? date('d/m/Y', strtotime($usuario['data_cadastro'])) : 'Não informada';
                                        ?>
                                        <tr class="table-row-oliver">
                                            <td class="px-6 py-4 text-id-gold">#<?php echo $usuario['id']; ?></td>
                                            <td class="px-6 py-4 text-cliente-dark"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                            <td class="px-6 py-4 text-zinc-600"><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td class="px-6 py-4 text-zinc-600">
                                                <span class="bg-zinc-100 border border-zinc-200 text-zinc-700 px-2.5 py-1 rounded text-xs font-medium">
                                                    <i class="far fa-calendar-alt mr-1 text-zinc-500"></i> <?php echo $data_formatada; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center space-x-3 text-zinc-500">
                                                <a href="pagADM/editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="hover:text-amber-600 transition-colors"><i class="fas fa-edit"></i></a>
                                                <a href="../Php/deletar_usuario.php?id=<?php echo $usuario['id']; ?>" class="hover:text-red-600 transition-colors" onclick="return confirm('Deseja realmente excluir o usuário <?php echo htmlspecialchars($usuario['nome']); ?>?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="px-6 py-8 text-center text-zinc-400 bg-white">Nenhum usuário cadastrado no momento.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="px-6 py-4 bg-zinc-50 border-t border-zinc-200 flex items-center justify-between">
                            <div class="text-xs text-zinc-500 font-medium">
                                Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?> (Total: <?php echo $total_usuarios; ?> cadastros)
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($pagina_atual > 1): ?>
                                    <a href="indexADM.php?pagina=<?php echo $pagina_atual - 1; ?>" class="px-3 py-1.5 text-xs font-semibold rounded bg-zinc-200 text-zinc-700 hover:bg-amber-500 hover:text-black transition-colors">
                                        <i class="fas fa-chevron-left mr-1"></i> Anterior
                                    </a>
                                <?php endif; ?>

                                <?php if ($pagina_atual < $total_paginas): ?>
                                    <a href="indexADM.php?pagina=<?php echo $pagina_atual + 1; ?>" class="px-3 py-1.5 text-xs font-semibold rounded bg-oliver-dark text-oliver-gold hover:bg-amber-500 hover:text-black transition-colors">
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