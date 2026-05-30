<?php
session_start();
include_once('../config.php'); // Caminho correto: sobe 1 nível para achar o config.php na raiz

// Pega o e-mail da sessão se o usuário estiver logado
$emailUsuario = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : '';
$cliente_id = 0;

if (!empty($emailUsuario)) {
    // Busca o ID do cliente correspondente no banco
    $query_usuario = "SELECT id FROM usuarios WHERE email = '$emailUsuario'";
    $resultado_usuario = mysqli_query($conexao, $query_usuario);
    if ($resultado_usuario && mysqli_num_rows($resultado_usuario) > 0) {
        $dados_usuario = mysqli_fetch_assoc($resultado_usuario);
        $cliente_id = $dados_usuario['id'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oliver'CelL - Meu Carrinho</title>
    <link rel="icon" href="../imagens/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Css/acessorios.css">
    <link rel="stylesheet" href="../Css/carrinho.css">
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

    <main class="cart-container">
        <div class="cart-title-section">
            <h1><i class="fas fa-shopping-cart"></i> SEU CARRINHO</h1>
            <p class="section-subtitle">Confira seus itens antes de finalizar o pedido via WhatsApp</p>
        </div>

        <div id="cart-content-wrapper">
            <div id="lista-carrinho"></div>
            
            <div class="cart-summary" id="cart-summary-box" style="display:none; margin-top: 20px; padding: 20px; background: #050505; border: 1px solid #ffcc00; border-radius: 8px;">
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1; text-align: left;">
                        <label for="cliente_nome" style="color: #fff; font-weight: bold; display: block; margin-bottom: 8px; font-size: 0.95rem;">Nome:</label>
                        <input type="text" id="cliente_nome" placeholder="Seu nome" style="width: 100%; padding: 10px; background: #111; color: #fff; border: 1px solid #333; border-radius: 4px; font-family: inherit;">
                    </div>
                    <div style="flex: 1; text-align: left;">
                        <label for="cliente_sobrenome" style="color: #fff; font-weight: bold; display: block; margin-bottom: 8px; font-size: 0.95rem;">Sobrenome:</label>
                        <input type="text" id="cliente_sobrenome" placeholder="Seu sobrenome" style="width: 100%; padding: 10px; background: #111; color: #fff; border: 1px solid #333; border-radius: 4px; font-family: inherit;">
                    </div>
                </div>

                <div style="margin-bottom: 20px; text-align: left;">
                    <label for="forma_pagamento" style="color: #fff; font-weight: bold; display: block; margin-bottom: 8px; font-size: 0.95rem;">Escolha a Forma de Pagamento:</label>
                    <select id="forma_pagamento" style="width: 100%; padding: 10px; background: #111; color: #fff; border: 1px solid #333; border-radius: 4px; font-family: inherit;">
                        <option value="Pix">Pix</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Dinheiro">Dinheiro</option>
                    </select>
                </div>

                <div id="cart-total-display" style="text-align: right; font-size: 1.4rem; font-weight: bold; color: #fff; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #222;"></div>
                
                <button class="btn-checkout" onclick="enviarParaWhatsApp()" style="width: 100%; padding: 12px; background: #25d366; color: #fff; border: none; font-weight: bold; font-size: 1.1rem; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 15px;">
                    <i class="fab fa-whatsapp"></i> Finalizar Pedido no WhatsApp
                </button>
                <div class="clear-container" style="text-align: center;">
                    <button class="btn-clear" onclick="limparCarrinho()" style="background: transparent; color: #ff4444; border: 1px solid #ff4444; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                        <i class="fas fa-trash-alt"></i> Limpar Todo o Carrinho
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Oliver'CelL. Todos os direitos reservados.</p>
    </footer>

    <script>
        const clienteIdLogado = <?php echo intval($cliente_id); ?>;

        function obterCarrinho() {
            return JSON.parse(localStorage.getItem('oliver_cart')) || [];
        }

        function renderizarCarrinho() {
            const carrinho = obterCarrinho();
            const listaDiv = document.getElementById('lista-carrinho');
            const resumoBox = document.getElementById('cart-summary-box');
            
            if(carrinho.length === 0) {
                listaDiv.innerHTML = `
                    <div class="cart-empty" style="text-align: center; padding: 40px 20px;">
                        <i class="fas fa-shopping-basket" style="font-size: 3rem; color: #ffcc00; margin-bottom: 15px;"></i>
                        <p style="font-size: 1.2rem; color: #aaa; margin-bottom: 20px;">Seu carrinho está vazio.</p>
                        <a href="Pag-php/acessorios.php" class="btn-back" style="display: inline-block; background: #ffcc00; color: #000; padding: 10px 20px; font-weight: bold; text-decoration: none; border-radius: 5px;">Ver Acessórios</a>
                    </div>
                `;
                resumoBox.style.display = "none";
                return;
            }

            resumoBox.style.display = "block";
            listaDiv.innerHTML = "";
            let totalGeral = 0;

            carrinho.forEach((item, index) => {
                let imagemSrc = item.imagem || "../imagens/logo.png";
                
                if (imagemSrc.startsWith('../../')) {
                    imagemSrc = imagemSrc.replace('../../', '../');
                }

                const precoUnitario = item.preco || 0;
                const subtotalItem = precoUnitario * item.qtd;
                totalGeral += subtotalItem;

                const itemRow = document.createElement('div');
                itemRow.className = "cart-item-row";
                itemRow.style.cssText = "display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #111; background: #000; margin-bottom: 10px; border-radius: 6px; border: 1px solid #222;";
                
                itemRow.innerHTML = `
                    <div class="item-info" style="display: flex; align-items: center;">
                        <img src="${imagemSrc}" alt="${item.nome}" class="item-img-thumb" style="width: 70px; height: 70px; object-fit: contain; margin-right: 15px; background: #0a0a0a; padding: 5px; border-radius: 5px; border: 1px solid #ffcc00;">
                        <div class="item-text-details">
                            <span class="item-name" style="font-weight: bold; display: block; color: #fff; font-size: 1rem;">${item.nome}</span>
                            <span class="item-price" style="color: #ffcc00; font-size: 0.9rem;">
                                ${precoUnitario.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })} cada
                            </span>
                        </div>
                    </div>
                    <div class="item-controls" style="display: flex; align-items: center; gap: 20px;">
                        <div class="qty-selector" style="display: flex; align-items: center; gap: 10px; background: #111; padding: 5px 10px; border-radius: 4px; border: 1px solid #333;">
                            <button class="btn-qty" onclick="mudarQtd(${index}, -1)" style="background: transparent; color: #ffcc00; border: none; font-size: 1.2rem; cursor: pointer; width: 20px;">-</button>
                            <span class="item-qtd" style="color: #fff; font-weight: bold; min-width: 20px; text-align: center;">${item.qtd}</span>
                            <button class="btn-qty" onclick="mudarQtd(${index}, 1)" style="background: transparent; color: #ffcc00; border: none; font-size: 1.2rem; cursor: pointer; width: 20px;">+</button>
                        </div>
                        <span class="item-subtotal" style="font-weight: bold; color: #fff; min-width: 90px; text-align: right;">
                            ${subtotalItem.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}
                        </span>
                        <button class="btn-remove" onclick="removerItem(${index})" title="Excluir produto" style="background: transparent; color: #ff4444; border: none; cursor: pointer; font-size: 1.1rem; margin-left: 10px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                listaDiv.appendChild(itemRow);
            });

            const totalDisplay = document.getElementById('cart-total-display');
            totalDisplay.innerHTML = `Total do Pedido: <span style="color: #ffcc00;">${totalGeral.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>`;
        }

        function mudarQtd(index, valor) {
            let carrinho = obterCarrinho();
            carrinho[index].qtd += valor;
            if(carrinho[index].qtd <= 0) {
                carrinho.splice(index, 1);
            }
            localStorage.setItem('oliver_cart', JSON.stringify(carrinho));
            renderizarCarrinho();
        }

        function removerItem(index) {
            let carrinho = obterCarrinho();
            carrinho.splice(index, 1);
            localStorage.setItem('oliver_cart', JSON.stringify(carrinho));
            renderizarCarrinho();
        }

        function limparCarrinho() {
            if(confirm("Tem certeza que deseja limpar todo o seu carrinho?")) {
                localStorage.removeItem('oliver_cart');
                renderizarCarrinho();
            }
        }

        function enviarParaWhatsApp() {
            const carrinho = obterCarrinho();
            if(carrinho.length === 0) return;

            if(clienteIdLogado === 0) {
                alert('Atenção: Você precisa estar logado para processar o pedido no seu perfil.');
                return;
            }

            const nomeCliente = document.getElementById('cliente_nome').value.trim();
            const sobrenomeCliente = document.getElementById('cliente_sobrenome').value.trim();
            const formaPagamento = document.getElementById('forma_pagamento').value;

            if (nomeCliente === "" || sobrenomeCliente === "") {
                alert("Por favor, preencha o seu Nome e Sobrenome antes de finalizar.");
                return;
            }

            // Aponta para a pasta global Php/ da raiz do projeto
            fetch('../Php/salvar_pedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cliente_id: clienteIdLogado,
                    carrinho: carrinho,
                    forma_pagamento: formaPagamento,
                    nome_cliente: nomeCliente,
                    sobrenome_cliente: sobrenomeCliente
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    let totalGeral = 0;
                    let mensagem = `Olá Oliver'CelL! Meu nome é *${nomeCliente} ${sobrenomeCliente}*.\n`;
                    mensagem += `Gostaria de encomendar (Pedido Nº #${data.pedido_id}):\n\n`;
                    
                    carrinho.forEach(item => {
                        const precoUnitario = item.preco || 0;
                        const subtotalItem = precoUnitario * item.qtd;
                        totalGeral += subtotalItem;
                        
                        mensagem += `▪️ *${item.qtd}x* ${item.nome}\n`;
                        mensagem += `   _Valor: ${precoUnitario.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}_\n\n`;
                    });
                    
                    mensagem += `*Forma de Pagamento:* ${formaPagamento}\n`;
                    mensagem += `*Total do Pedido:* ${totalGeral.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}\n\n`;
                    mensagem += "Por favor, confirmem a disponibilidade para retirada!";

                    localStorage.removeItem('oliver_cart');
                    renderizarCarrinho();

                    const linkWhatsApp = `https://wa.me/5561991857131?text=${encodeURIComponent(mensagem)}`;
                    window.open(linkWhatsApp, '_blank');
                } else {
                    alert('Erro ao processar o registro do pedido: ' + data.erro);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Não foi possível registrar o pedido no sistema. Verifique sua conexão.');
            });
        }

        renderizarCarrinho();
    </script>
</body>
</html>