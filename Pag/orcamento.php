<?php
session_start();

// CORREÇÃO AQUI: Voltando duas pastas para encontrar o config.php na raiz
include_once('../config.php');

// Verifica de forma rigorosa se a sessão do e-mail do usuário está ativa
$usuarioLogado = (isset($_SESSION['usuario_email']) && !empty($_SESSION['usuario_email'])) ? true : false;

// Verifica se a requisição veio do JavaScript para salvar no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_orcamento'])) {
    // Só deixa salvar se estiver logado de verdade por segurança no backend
    if (!$usuarioLogado) {
        echo json_encode(['status' => 'erro', 'detalhes' => 'Sessão inválida.']);
        exit();
    }

    if (isset($conexao)) {
        $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
        $modelo = mysqli_real_escape_string($conexao, $_POST['modelo']);
        $defeito = mysqli_real_escape_string($conexao, $_POST['defeito']);
        $data_solicitacao = date('Y-m-d H:i:s');

        // Insere na tabela 'orcamentos'
        $query = "INSERT INTO orcamentos (nome, modelo, defeito, data_solicitacao) VALUES ('$nome', '$modelo', '$defeito', '$data_solicitacao')";
        
        if (mysqli_query($conexao, $query)) {
            echo json_encode(['status' => 'sucesso']);
        } else {
            echo json_encode(['status' => 'erro', 'detalhes' => mysqli_error($conexao)]);
        }
    } else {
        echo json_encode(['status' => 'erro', 'detalhes' => 'Conexão com o banco falhou.']);
    }
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oliver'CelL - Orçamento</title>
    <link rel="icon" href="../imagens/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/orcamento.css?v=2">
</head>
<body>

<header class="header">
    <div class="header-container">
        <a href="../index.php" class="logo-link">
            <img src="../imagens/logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">Oliver'<span>CelL</span></span>
        </a>
    </div>
</header>

<main class="main-content">
    <div class="auth-container">
        
        <div class="info-side">
            <h3>Orçamento Rápido</h3>
            <p>Ao clicar em enviar, seu orçamento será salvo em nosso sistema e seu WhatsApp abrirá automaticamente com os detalhes.</p>
            <i class="fab fa-whatsapp" style="font-size: 4rem; color: #25D366; margin-top: 20px;"></i>
        </div>

        <div class="form-side">
            <h2 class="form-title">Dados do Aparelho</h2>
            <p class="form-subtitle">Preencha abaixo os detalhes</p>
            
            <form id="orcamentoForm" onsubmit="event.preventDefault(); verificarOrcamento();">
                <input type="text" id="nome" class="form-input" placeholder="Seu Nome" required>
                <input type="text" id="modelo" class="form-input" placeholder="Modelo do Aparelho" required>
                <textarea id="defeito" class="form-input" placeholder="O que aconteceu com o aparelho?" rows="4" required></textarea>
                
                <div class="cta-section">
                    <button type="submit" class="auth-btn btn-perfil-verde" style="border: none; cursor: pointer; width: 100%;">
                        <i class="fab fa-whatsapp"></i> PEDIR ORÇAMENTO VIA WHATSAPP
                    </button>

                    <a href="../index.php" class="auth-btn btn-perfil-vermelho" style="text-align: center;">
                        <i class="fas fa-arrow-left"></i> VOLTAR PARA O INÍCIO
                    </a>
                </div>
            </form>
        </div>

    </div>
</main>

<footer>
    <p>&copy; 2026 Oliver'CelL - Todos os direitos reservados.</p>
</footer>

<script>
// Transforma a validação do PHP em uma constante booleana do JS
const usuarioEstaLogado = <?php echo $usuarioLogado ? 'true' : 'false'; ?>;

function verificarOrcamento() {
    // 🚨 TRAVA DE SEGURANÇA REPETINDO EXATAMENTE A MESMA FRASE E AÇÃO
    if (!usuarioEstaLogado) {
        alert("Para adicionar produtos ao carrinho e garantir os seus acessórios, você precisa fazer o login ou criar o seu cadastro primeiro!");
        window.location.href = window.location.origin + "/OliverCell/Clientes/Login.php";
        return;
    }

    // Se estiver logado, segue o fluxo natural de envio do formulário
    gerarOrcamento();
}

function gerarOrcamento() {
    const nome = document.getElementById('nome').value.trim();
    const modelo = document.getElementById('modelo').value.trim();
    const defeito = document.getElementById('defeito').value.trim();
    
    if(!nome || !modelo || !defeito) {
        alert("Por favor, preencha todos os campos do orçamento.");
        return;
    }

    const formData = new FormData();
    formData.append('ajax_orcamento', '1');
    formData.append('nome', nome);
    formData.append('modelo', modelo);
    formData.append('defeito', defeito);

    // Envia os dados para salvar no banco em background
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const meuNumero = "5561991857131"; // Atualizado com o 9 extra de telefone corrigido
        const textoFinal = `*NOVO ORÇAMENTO - OLIVER'CELL* 📱\n\n` +
                           `*Cliente:* ${nome}\n` +
                           `*Aparelho:* ${modelo}\n` +
                           `*Problema:* ${defeito}\n\n` +
                           `_Enviado via site Oliver'CelL_`;

        const url = `https://wa.me/${meuNumero}?text=${encodeURIComponent(textoFinal)}`;
        
        if(data.status === 'sucesso') {
            window.open(url, '_blank');
        } else {
            console.error("Erro interno ao salvar no banco:", data.detalhes);
            window.open(url, '_blank');
        }
    })
    .catch(error => {
        console.error("Erro na requisição:", error);
        // Fallback para abrir o WhatsApp mesmo em caso de erro de conexão
        const meuNumero = "5561991857131";
        const textoFinal = `*NOVO ORÇAMENTO - OLIVER'CELL* 📱\n\n*Cliente:* ${nome}\n*Aparelho:* ${modelo}\n*Problema:* ${defeito}`;
        window.open(`https://wa.me/${meuNumero}?text=${encodeURIComponent(textoFinal)}`, '_blank');
    });
}
</script>

</body>
</html>