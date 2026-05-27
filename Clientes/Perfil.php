<?php
session_start();
include_once('../config.php');

if (!isset($_SESSION['usuario_email'])) {
    header("Location: ../Login.php");
    exit();
}

$emailUsuario = $_SESSION['usuario_email'];
$diretorio = "uploads/";

// --- LÓGICA DE PROCESSAMENTO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. AÇÃO DE EXCLUIR FOTO
    if (isset($_POST['excluir_foto'])) {
        $buscaFoto = mysqli_query($conexao, "SELECT foto FROM usuarios WHERE email = '$emailUsuario'");
        $dadosFoto = mysqli_fetch_assoc($buscaFoto);
        
        if (!empty($dadosFoto['foto'])) {
            $arquivo = $diretorio . $dadosFoto['foto'];
            if (file_exists($arquivo)) unlink($arquivo);
            mysqli_query($conexao, "UPDATE usuarios SET foto = NULL WHERE email = '$emailUsuario'");
        }
        header("Location: Perfil.php");
        exit();
    }

    // 2. AÇÃO DE SALVAR (UPLOAD OU CÂMERA)
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
        header("Location: Perfil.php");
        exit();
    }
}

// BUSCA DADOS ATUALIZADOS
$query = "SELECT nome, foto FROM usuarios WHERE email = '$emailUsuario'";
$resultado = mysqli_query($conexao, $query);
$dados = mysqli_fetch_assoc($resultado);

$nomeCompleto = $dados['nome'] ?? "Usuário";
$fotoBD = !empty($dados['foto']) ? "uploads/" . $dados['foto'] : "";
$primeiroNome = explode(' ', trim($nomeCompleto))[0];

// Captura a primeira letra do nome para o avatar de texto
$inicialNome = !empty($primeiroNome) ? strtoupper(substr($primeiroNome, 0, 1)) : "U";
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
        /* CSS PARA ALINHAMENTO E EXIBIÇÃO DA INICIAL */
        .avatar-circle { 
            width: 130px; height: 130px; border-radius: 50%; 
            border: 3px solid #ffcc00; margin: 0 auto 15px; 
            overflow: hidden; background: #111; 
            display: flex; align-items: center; justify-content: center; 
        }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Estilização da Letra Inicial quando não há foto */
        .avatar-letra {
            font-size: 56px;
            font-weight: 800;
            color: #ffcc00;
            font-family: 'Arial', sans-serif;
            user-select: none;
        }

        .acoes-perfil { margin-bottom: 20px; display: flex; justify-content: center; }
        .btn-group-foto { display: flex; gap: 10px; justify-content: center; }

        .btn-icon-yellow {
            background-color: #ffcc00; color: #000; border: none;
            padding: 10px 15px; border-radius: 5px; cursor: pointer;
            font-size: 16px; transition: 0.3s;
        }
        .btn-icon-yellow:hover { background-color: #fff; transform: translateY(-2px); }

        .btn-icon-danger {
            background-color: #ff4444; color: #fff; border: none;
            padding: 10px 15px; border-radius: 5px; cursor: pointer;
            font-size: 16px; transition: 0.3s;
        }
        .btn-icon-danger:hover { background-color: #cc0000; transform: translateY(-2px); }

        #area-camera { 
            display: none; background: #000; border: 2px solid #ffcc00; 
            margin: 10px auto; padding: 10px; border-radius: 8px; max-width: 200px;
        }
        video { width: 100%; border-radius: 5px; }

        .cliente-vip { color: #ffcc00; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; }
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
            <a href="Pag-php/orcamento.php" class="side-link"><i class="fas fa-tools"></i> Novo Orçamento</a>
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
                    <span class="status-num">05</span>
                    <span class="status-label">SERVIÇOS FEITOS</span>
                </div>
            </div>
            <button onclick="window.location.href='cliente.html'" class="btn-voltar">VOLTAR PARA A LOJA</button>
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
    
    // Finaliza o uso da câmera e envia o formulário automaticamente
    if (streamGlobal) {
        streamGlobal.getTracks().forEach(track => track.stop());
    }
    document.getElementById('formGeral').submit();
}
</script>

</body>
</html>