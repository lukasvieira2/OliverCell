<?php
session_start();
include_once('../config.php');

// CORREÇÃO: Se não houver sessão, vai para o login.php correto que fica na mesma pasta
if (!isset($_SESSION['usuario_email'])) {
    header("Location: login.php");
    exit();
}

$emailUsuario = $_SESSION['usuario_email'];
$diretorio = "uploads/";

// Mantém a correção automática caso entrem novos pedidos sem ID vinculado
mysqli_query($conexao, "UPDATE pedidos SET cliente_id = 13 WHERE cliente_id = '' OR cliente_id IS NULL OR cliente_id = 0");

// --- LÓGICA DE PROCESSAMENTO DE FOTOS (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir_foto'])) {
        $buscaFoto = mysqli_query($conexao, "SELECT foto FROM usuarios WHERE email = '$emailUsuario'");
        $dadosFoto = mysqli_fetch_assoc($buscaFoto);
        if (!empty($dadosFoto['foto'])) {
            $arquivo = $diretorio . $dadosFoto['foto'];
            if (file_exists($arquivo)) unlink($arquivo);
            mysqli_query($conexao, "UPDATE usuarios SET foto = NULL WHERE email = '$emailUsuario'");
        }
        header("Location: perfil.php");
        exit();
    }

    $imagemFinal = null;
    if (!empty($_POST['foto_base64'])) {
        $data = base64_decode(explode(',', $_POST['foto_base64'])[1]);
        $nomeArquivo = md5($emailUsuario . time()) . '.png';
        file_put_contents($diretorio . $nomeArquivo, $data);
        $imagemFinal = $nomeArquivo;
    } elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $nomeArquivo = md5($emailUsuario . time()) . "." . $extensao;
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $diretorio . $nomeArquivo)) {
            $imagemFinal = $nomeArquivo;
        }
    }

    if ($imagemFinal) {
        mysqli_query($conexao, "UPDATE usuarios SET foto = '$imagemFinal' WHERE email = '$emailUsuario'");
        header("Location: perfil.php");
        exit();
    }
}

// BUSCA DADOS ATUALIZADOS DO USUÁRIO
$query = "SELECT id, nome, foto FROM usuarios WHERE email = '$emailUsuario'";
$resultado = mysqli_query($conexao, $query);
$dados = mysqli_fetch_assoc($resultado);

$usuarioId = isset($dados['id']) ? intval($dados['id']) : 0;
$nomeCompleto = $dados['nome'] ?? "Usuário";
$fotoBD = !empty($dados['foto']) ? "uploads/" . $dados['foto'] : "";
$primeiroNome = explode(' ', trim($nomeCompleto))[0];
$inicialNome = !empty($primeiroNome) ? strtoupper(substr($primeiroNome, 0, 1)) : "U";

// BUSCA APENAS OS PEDIDOS ATIVOS (Pendente ou Confirmado) para esta tela
$query_pedidos = "SELECT * FROM pedidos WHERE cliente_id = $usuarioId AND status IN ('Pendente', 'Confirmado') ORDER BY id DESC";
$resultado_pedidos = mysqli_query($conexao, $query_pedidos);
$total_pedidos_ativos = 0;
if ($resultado_pedidos) {
    $total_pedidos_ativos = mysqli_num_rows($resultado_pedidos);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Oliver'CelL</title>
    <link rel="icon" href="../imagens/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/perfil.css">
    <style>
        .avatar-circle { 
            width: 130px; height: 130px; border-radius: 50%; 
            border: 3px solid #ffcc00; margin: 0 auto 15px; 
            overflow: hidden; background: #111; 
            display: flex; align-items: center; justify-content: center; 
        }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-letra { font-size: 56px; font-weight: 800; color: #ffcc00; font-family: 'Arial', sans-serif; user-select: none; }
        .acoes-perfil { margin-bottom: 20px; display: flex; justify-content: center; }
        .btn-group-foto { display: flex; gap: 10px; justify-content: center; }
        .btn-icon-yellow { background-color: #ffcc00; color: #000; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-icon-yellow:hover { background-color: #fff; transform: translateY(-2px); }
        .btn-icon-danger { background-color: #ff4444; color: #fff; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-icon-danger:hover { background-color: #cc0000; transform: translateY(-2px); }
        #area-camera { display: none; background: #000; border: 2px solid #ffcc00; margin: 10px auto; padding: 10px; border-radius: 8px; max-width: 200px; }
        video { width: 100%; border-radius: 5px; }
        .cliente-vip { color: #ffcc00; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; }

        .pedidos-section { margin-top: 30px; background: #0a0a0a; border: 1px solid #222; border-radius: 8px; padding: 20px; text-align: left; }
        .pedido-card { background: #000; border: 1px solid #333; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .pedido-card:last-child { margin-bottom: 0; }
        .pedido-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 10px; margin-bottom: 10px; }
        .pedido-codigo { font-weight: bold; color: #ffcc00; font-size: 0.95rem; }
        .pedido-status { font-size: 0.75rem; font-weight: bold; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; }
        
        .status-pendente { background: rgba(255, 204, 0, 0.1); color: #ffcc00; border: 1px solid #ffcc00; }
        .status-confirmado { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6; }
        
        .pedido-itens { font-size: 0.85rem; color: #bbb; line-height: 1.5; margin-bottom: 10px; }
        .pedido-total { text-align: right; font-size: 0.9rem; color: #fff; }
        .pedido-total strong { color: #25d366; }
    </style>
</head>
<body>

<header class="header">
    <div class="header-container">
        <a href="cliente.html" class="logo-link">
            <img src="../imagens/logo.png" alt="Logo" class="logo-img">
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

            <div class="acoes-perfil">
                <form id="formGeral" method="POST" enctype="multipart/form-data">
                    <input type="file" name="foto_perfil" id="foto_perfil" style="display:none;" onchange="document.getElementById('formGeral').submit()">
                    <input type="hidden" name="foto_base64" id="foto_base64">
                    
                    <div class="btn-group-foto">
                        <button type="button" class="btn-icon-yellow" onclick="document.getElementById('foto_perfil').click()" title="Escolher Arquivo">
                            <i class="fas fa-upload"></i>
                        </button>
                        <button type="button" class="btn-icon-yellow" onclick="abrirCamera()" title="Tirar Foto Agora">
                            <i class="fas fa-camera"></i>
                        </button>
                        
                        <?php if($fotoBD && file_exists($fotoBD)): ?>
                        <button type="submit" name="excluir_foto" class="btn-icon-danger" title="Remover Foto de Perfil" onclick="return confirm('Deseja realmente remover sua foto de perfil?')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div id="area-camera">
                <video id="video" autoplay></video>
                <button type="button" class="btn-icon-yellow" style="width: 100%; margin-top: 10px; background: #25d366; color: #fff;" onclick="capturarFoto()">
                    <i class="fas fa-check"></i> CONFIRMAR
                </button>
                <canvas id="canvas" style="display:none;"></canvas>
            </div>

            <h3><?php echo htmlspecialchars(strtoupper($primeiroNome)); ?></h3>
            <p class="cliente-vip">Cliente VIP</p>
            <hr class="divider">
            
            <a href="cliente.html" class="side-link"><i class="fas fa-home"></i> Painel Inicial</a>
            <a href="Pag-php/historico_orcamento.php" class="side-link"><i class="fas fa-tools"></i> Histórico orçamento </a>
            <a href="Pag-php/pedidos.php" class="side-link"><i class="fas fa-history"></i> Histórico de Pedidos</a>
            <a href="trocar_senha.php" class="side-link"><i class="fas fa-key"></i> Alterar Senha</a>
            <a href="logout.php" class="side-link logout" style="color: #ff4444;"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>

        <div class="perfil-main">
            <h2 class="title">MEUS DADOS</h2>
            <div class="info-group">
                <label>NOME COMPLETO</label>
                <div class="info-box"><?php echo htmlspecialchars($nomeCompleto); ?></div>
            </div>
            <div class="info-group">
                <label>E-MAIL (LOGIN)</label>
                <div class="info-box"><?php echo htmlspecialchars($emailUsuario); ?></div>
            </div>

            <div class="status-section">
                <div class="status-card">
                    <span class="status-num">01</span>
                    <span class="status-label">APARELHO EM REPARO</span>
                </div>
                <div class="status-card">
                    <span class="status-num"><?php echo str_pad($total_pedidos_ativos, 2, "0", STR_PAD_LEFT); ?></span>
                    <span class="status-label">PEDIDOS ATIVOS</span>
                </div>
            </div>

            <div class="pedidos-section">
                <h3 style="color: #ffcc00; margin-bottom: 15px; font-size: 1.1rem; letter-spacing: 0.5px;">
                    <i class="fas fa-truck-loading" style="margin-right: 8px;"></i> PEDIDOS EM ANDAMENTO
                </h3>

                <?php if ($total_pedidos_ativos === 0): ?>
                    <p style="color: #666; font-size: 0.9rem; text-align: center; padding: 10px 0;">Não há pedidos em andamento no momento.</p>
                <?php else: ?>
                    <div class="pedidos-lista">
                        <?php while ($pedido = mysqli_fetch_assoc($resultado_pedidos)): ?>
                            <div class="pedido-card">
                                <div class="pedido-header">
                                    <span class="pedido-codigo">CÓDIGO: #<?php echo $pedido['id']; ?></span>
                                    
                                    <?php if (($pedido['status'] ?? 'Pendente') === 'Confirmado'): ?>
                                        <span class="pedido-status status-confirmado"><i class="fas fa-check-circle"></i> Confirmado</span>
                                    <?php else: ?>
                                        <span class="pedido-status status-pendente"><i class="fas fa-clock"></i> Pendente</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="pedido-itens">
                                    <?php 
                                    $id_pedido = intval($pedido['id']);
                                    $query_itens = "SELECT * FROM itens_pedido WHERE pedido_id = $id_pedido";
                                    $res_itens = mysqli_query($conexao, $query_itens);
                                    
                                    if ($res_itens) {
                                        while ($item = mysqli_fetch_assoc($res_itens)) {
                                            echo "• " . $item['quantidade'] . "x " . htmlspecialchars($item['produto_nome']) . "<br>";
                                        }
                                    }
                                    ?>
                                </div>
                                
                                <div class="pedido-total">
                                    Total: <strong>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></strong>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='cliente.html';" class="btn-voltar" style="margin-top: 25px;">VOLTAR PARA A LOJA</button>
        </div>
    </div>
</main>

<script>
let video = document.getElementById('video');
let canvas = document.getElementById('canvas');
let areaCamera = document.getElementById('area-camera');
let streamGlobal = null;

function abrirCamera() {
    areaCamera.style.display = 'block';
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { 
            video.srcObject = stream; 
            streamGlobal = stream;
        })
        .catch(err => { alert("Não foi possível acessar a câmera."); });
}

function capturarFoto() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    document.getElementById('foto_base64').value = canvas.toDataURL('image/png');
    
    if (streamGlobal) {
        streamGlobal.getTracks().forEach(track => track.stop());
    }
    document.getElementById('formGeral').submit();
}
</script>

</body>
</html>