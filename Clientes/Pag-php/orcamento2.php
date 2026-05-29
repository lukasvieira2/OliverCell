<?php
session_start();

// Voltando duas pastas para encontrar o config.php na raiz do projeto (Conforme sua árvore de arquivos)
include_once('../../config.php');

// Verifica se a requisição veio do JavaScript via AJAX para salvar no banco
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_orcamento'])) {
    // Garante que a conexão existe antes de tratar os dados
    if (isset($conexao)) {
        $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
        $modelo = mysqli_real_escape_string($conexao, $_POST['modelo']);
        $defeito = mysqli_real_escape_string($conexao, $_POST['defeito']);
        $data_solicitacao = date('Y-m-d H:i:s');
        $status = 'Pendente'; // Alinhado com a coluna padrão observada na sua tabela do banco de dados

        // Insere na tabela 'orcamentos' mapeando todas as colunas necessárias
        $query = "INSERT INTO orcamentos (nome, modelo, defeito, data_solicitacao, status) 
                  VALUES ('$nome', '$modelo', '$defeito', '$data_solicitacao', '$status')";
        
        if (mysqli_query($conexao, $query)) {
            echo json_encode(['status' => 'sucesso']);
        } else {
            echo json_encode(['status' => 'erro', 'detalhes' => mysqli_error($conexao)]);
        }
    } else {
        echo json_encode(['status' => 'erro', 'detalhes' => 'Conexão com o banco de dados falhou.']);
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
    <link rel="icon" href="../../imagens/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Css/orcamento.css">
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
    <div class="auth-container">
        
        <div class="info-side">
            <h3>Orçamento Rápido</h3>
            <p>Ao clicar em enviar, seu orçamento será salvo em nosso sistema e seu WhatsApp abrirá automaticamente com os detalhes.</p>
            <i class="fab fa-whatsapp" style="font-size: 4rem; color: #25D366; margin-top: 20px;"></i>
        </div>

        <div class="form-side">
            <h2 class="form-title">Dados do Aparelho</h2>
            <p class="form-subtitle">Preencha abaixo os detalhes</p>
            
            <form id="orcamentoForm">
                <input type="text" id="nome" class="form-input" placeholder="Seu Nome" required>
                <input type="text" id="modelo" class="form-input" placeholder="Modelo do Aparelho" required>
                <textarea id="defeito" class="form-input" placeholder="O que aconteceu com o aparelho?" rows="4" required></textarea>
                
                <div class="cta-section">
                    <button type="button" onclick="gerarOrcamento()" class="auth-btn btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> ENVIAR PELO MEU WHATSAPP
                    </button>

                    <a href="servicos.html" class="auth-btn btn-perfil-vermelho">
                        <i class="fas fa-user-circle"></i> VOLTAR PARA SERVIÇOS
                    </a>
                </div>
            </form>
        </div>

    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Oliver'CelL - Todos os direitos reservados.</p>
</footer>

<script>
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

    // Envia os dados assincronamente em background para rodar a lógica PHP estruturada no topo
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Número configurado com o '9' adicional do DDD 61 para funcionamento correto
        const meuNumero = "5561991857131";
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
            window.open(url, '_blank'); // Redireciona mesmo em caso de erro interno para não perder a venda
        }
    })
    .catch(error => {
        console.error("Erro na requisição AJAX:", error);
        // Fallback instantâneo caso ocorra falha de conectividade com o servidor
        const meuNumero = "5561991857131";
        const textoFinal = `*NOVO ORÇAMENTO - OLIVER'CELL* 📱\n\n*Cliente:* ${nome}\n*Aparelho:* ${modelo}\n*Problema:* ${defeito}`;
        window.open(`https://wa.me/${meuNumero}?text=${encodeURIComponent(textoFinal)}`, '_blank');
    });
}
</script>

</body>
</html>