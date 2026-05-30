<?php
session_start();
include_once('../../config.php'); // Voltando 2 níveis para achar o config.php na raiz do projeto

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_email'])) {
    header("Location: ../Login.php");
    exit();
}

$emailUsuario = $_SESSION['usuario_email'];

// --- LÓGICA DE EXCLUSÃO DE ORÇAMENTO FINALIZADO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_orcamento_id'])) {
    $orcamentoIdParaExcluir = intval($_POST['excluir_orcamento_id']);
    
    // Deleta o registro do orçamento do histórico do cliente
    mysqli_query($conexao, "DELETE FROM orcamentos WHERE id = $orcamentoIdParaExcluir");
    
    // Recarrega a página para atualizar a lista instantaneamente
    header("Location: orcamento.php");
    exit();
}

// BUSCA DADOS DO USUÁRIO LOGADO
$query = "SELECT id, nome, foto FROM usuarios WHERE email = '$emailUsuario'";
$resultado = mysqli_query($conexao, $query);
$dados = mysqli_fetch_assoc($resultado);

$usuarioId = isset($dados['id']) ? intval($dados['id']) : 0;
$nomeCompleto = $dados['nome'] ?? "Usuário";

// Tratamento do caminho da foto: subindo 1 nível para acessar 'Clientes/uploads/'
$fotoBD = !empty($dados['foto']) ? "../uploads/" . $dados['foto'] : "";
$primeiroNome = explode(' ', trim($nomeCompleto))[0];
$inicialNome = !empty($primeiroNome) ? strtoupper(substr($primeiroNome, 0, 1)) : "U";

// FILTRO: Carrega os orçamentos finalizados/concluídos vinculados ao cliente
$query_historico = "SELECT * FROM orcamentos WHERE (nome = '$nomeCompleto' OR id IN (SELECT id FROM orcamentos WHERE nome LIKE '%$primeiroNome%')) AND status IN ('Concluído', 'Concluido') ORDER BY id DESC";
$resultado_historico = mysqli_query($conexao, $query_historico);
$total_concluidos = 0;
if ($resultado_historico) {
    $total_concluidos = mysqli_num_rows($resultado_historico);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Orçamentos - Oliver'CelL</title>
    <link class="icon" rel="icon" href="../../imagens/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Css/perfil.css">
    <style>
        .avatar-circle { width: 130px; height: 130px; border-radius: 50%; border: 3px solid #ffcc00; margin: 0 auto 15px; overflow: hidden; background: #111; display: flex; align-items: center; justify-content: center; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-letra { font-size: 56px; font-weight: 800; color: #ffcc00; font-family: 'Arial', sans-serif; }
        .cliente-vip { color: #ffcc00; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; text-align: center; }
        
        .pedidos-section { margin-top: 30px; background: #0a0a0a; border: 1px solid #222; border-radius: 8px; padding: 20px; text-align: left; }
        .pedido-card { background: #000; border: 1px solid #333; border-radius: 6px; padding: 15px; margin-bottom: 15px; opacity: 0.9; position: relative; }
        .pedido-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 10px; margin-bottom: 10px; padding-right: 35px; }
        .pedido-codigo { font-weight: bold; color: #ffcc00; font-size: 0.95rem; }
        .pedido-status { font-size: 0.75rem; font-weight: bold; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; }
        
        .status-concluido { background: rgba(37, 211, 102, 0.1); color: #25d366; border: 1px solid #25d366; }
        .pedido-itens { font-size: 0.85rem; color: #bbb; line-height: 1.5; margin-bottom: 10px; }
        .pedido-total { text-align: right; font-size: 0.9rem; color: #fff; }
        .pedido-total strong { color: #25d366; }

        /* Estilo do botão da lixeira */
        .btn-excluir-pedido {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            color: #ff4444;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.2s;
            padding: 5px;
        }
        .btn-excluir-pedido:hover {
            color: #cc0000;
            transform: scale(1.15);
        }
    </style>
</head>
<body>

<header class="header">
    <div class="header-container">
        <a href="../cliente.html" class="logo-link">
            <img src="../../imagens/logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">Oliver'<span>CelL</span></span>
        </a>
    </div>
</header>

<main class="main-content">
    <div class="perfil-container">
        
        <div class="perfil-sidebar" style="text-align: center;">
            <div class="avatar-circle">
                <?php if($fotoBD && file_exists($fotoBD)): ?>
                    <img src="<?php echo $fotoBD; ?>" alt="Foto de Perfil">
                <?php else: ?>
                    <span class="avatar-letra"><?php echo $inicialNome; ?></span>
                <?php endif; ?>
            </div>

            <h3><?php echo htmlspecialchars(strtoupper($primeiroNome)); ?></h3>
            <p class="cliente-vip">Cliente VIP</p>
            <hr class="divider">
            
            <a href="../cliente.html" class="side-link"><i class="fas fa-home"></i> Painel Inicial</a>
            <a href="../Perfil.php" class="side-link"><i class="fas fa-user"></i> Meu Perfil / Ativos</a>
            <a href="historico_orcamento.php" class="side-link active" style="color: #ffcc00;"><i class="fas fa-tools"></i> Histórico orçamento </a>
            <a href="pedidos.php" class="side-link"><i class="fas fa-history"></i> Histórico de Pedidos</a>
            <a href="../logout.php" class="side-link logout" style="color: #ff4444;"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>

        <div class="perfil-main">
            <h2 class="title">HISTÓRICO DE ORÇAMENTOS</h2>

            <div class="status-section">
                <div class="status-card" style="width: 100%;">
                    <span class="status-num"><?php echo str_pad($total_concluidos, 2, "0", STR_PAD_LEFT); ?></span>
                    <span class="status-label">APARELHOS CONSERTADOS / FINALIZADOS</span>
                </div>
            </div>

            <div class="pedidos-section">
                <h3 style="color: #25d366; margin-bottom: 15px; font-size: 1.1rem; letter-spacing: 0.5px;">
                    <i class="fas fa-check-double" style="margin-right: 8px;"></i> HISTÓRICO GERAL
                </h3>

                <?php if ($total_concluidos === 0): ?>
                    <p style="color: #666; font-size: 0.9rem; text-align: center; padding: 10px 0;">Nenhum orçamento finalizado no seu histórico.</p>
                <?php else: ?>
                    <div class="pedidos-lista">
                        <?php while ($orcamento = mysqli_fetch_assoc($resultado_historico)): 
                            $data_orc = !empty($orcamento['data_solicitacao']) ? date('d/m/Y H:i', strtotime($orcamento['data_solicitacao'])) : 'Recente';
                            ?>
                            <div class="pedido-card">
                                
                                <form method="POST" onsubmit="return confirm('Tem certeza que deseja apagar este orçamento do seu histórico permanente?');">
                                    <input type="hidden" name="excluir_orcamento_id" value="<?php echo $orcamento['id']; ?>">
                                    <button type="submit" class="btn-excluir-pedido" title="Excluir orçamento do histórico">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                                <div class="pedido-header">
                                    <span class="pedido-codigo">ORDEM: #<?php echo $orcamento['id']; ?></span>
                                    <span class="pedido-status status-concluido">
                                        <i class="fas fa-check-circle"></i> Consertado
                                    </span>
                                </div>
                                
                                <div class="pedido-itens">
                                    <strong style="color: #ffcc00;">Aparelho:</strong> <?php echo htmlspecialchars($orcamento['modelo']); ?><br>
                                    <strong style="color: #ffcc00;">Defeito resolvido:</strong> <span style="font-style: italic;">"<?php echo htmlspecialchars($orcamento['defeito']); ?>"</span><br>
                                    <span style="color: #555; font-size: 0.8rem;">Finalizado em: <?php echo $data_orc; ?></span>
                                </div>
                                
                                <div class="pedido-total">
                                    Total Pago: <strong>R$ <?php echo number_format(($orcamento['preco'] ?? 0), 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button onclick="window.location.href='../Perfil.php'" class="btn-voltar" style="margin-top: 25px; border-color: #ffcc00; color: #ffcc00; background: transparent; cursor: pointer;">VOLTAR AO PERFIL</button>
        </div>
    </div>
</main>

</body>
</html>