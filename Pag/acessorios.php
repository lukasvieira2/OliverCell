<?php
session_start();

// Verifica de forma rigorosa se a sessão do e-mail do usuário está ativa
$usuarioLogado = (isset($_SESSION['usuario_email']) && !empty($_SESSION['usuario_email'])) ? true : false;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oliver'CelL - Acessórios Premium</title>
    <link rel="icon" href="../imagens/logo.png" type="image/png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/acessorios.css?v=5">
    <link rel="stylesheet" href="../css/header.css?v=5">
</head>
<body>

    <header class="header">
        <div class="header-container">
            <a href="../index.php" class="logo-link">
                <img src="../imagens/logo.png" alt="Logo" class="logo-img">
                <span class="logo-text">Oliver'<span>CelL</span></span>
            </a>
        
            <nav class="nav-principal">
                <ul class="menu-main">
                    <li><a href="../index.php">Início</a></li>
                    <li><a href="servicos.html">Serviços</a></li>
                    <li><a href="contato.html">Contato</a></li>
                    <li>
                        <a href="../carrinho.php" style="position: relative; display: inline-flex; align-items: center;">
                            <i class="fas fa-shopping-cart" style="color: #ffcc00; font-size: 18px;"></i>
                            <span id="cart-count" style="position: absolute; top: -10px; right: -12px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold;">0</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="products-container">
            <div class="premium-title-section">
                <h1>ACESSÓRIOS PREMIUM</h1>
                <p class="subtitle">PROTEÇÃO E ESTILO PARA SEU SMARTPHONE</p>
            </div>

            <div class="products-grid">
                
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/CaboCelularTipoC.jpg" alt="Cabo Tipo C">
                    </div>
                    <h3>CABO TIPO-C PREMIUM</h3>
                    <p>Cabo de alta velocidade e durabilidade Masterdrive para conexões Tipo-C.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('CABO TIPO-C PREMIUM')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/cabocelularlightning .png" alt="Cabo Lightning">
                    </div>
                    <h3>CABO LIGHTNING IPHONE</h3>
                    <p>Cabo Masterdrive premium ideal para carga rápida e segura no seu iPhone.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('CABO LIGHTNING IPHONE')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/foneBranco.png" alt="Fone Branco">
                    </div>
                    <h3>FONE ESTÉREO BRANCO</h3>
                    <p>Som Premium balanceado com super bass da Alpha Gold na cor branca.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('FONE ESTÉREO BRANCO')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/fonePreto.png" alt="Fone Preto">
                    </div>
                    <h3>FONE ESTÉREO PRETO</h3>
                    <p>Fone Alpha Gold de ouvido estéreo com excelente isolamento e graves profundos.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('FONE ESTÉREO PRETO')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/CarMountAirVentHolder.png" alt="Suporte Veicular">
                    </div>
                    <h3>SUPORTE VEICULAR 360°</h3>
                    <p>Suporte Peining articulado com rotação completa para fixação estável no painel.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('SUPORTE VEICULAR 360°')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/CA41.jpeg" alt="Carregador Inova CA41">
                    </div>
                    <h3>CARREGADOR INOVA CA41</h3>
                    <p>Carregador rápido e seguro de alta performance para o dia a dia.</p>
                    <p class="preco">R$ 20,00</p>
                    <button onclick="verificarAcesso('CARREGADOR INOVA CA41')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/FoneBluetooth.jpeg" alt="Fone Bluetooth Premium">
                    </div>
                    <h3>FONE BLUETOOTH TWS</h3>
                    <p>Fone de ouvido sem fio de alta fidelidade com excelente autonomia de bateria.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('FONE BLUETOOTH TWS')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/Pei-185-3.jpeg" alt="Cabo Peining 3 em 1">
                    </div>
                    <h3>CABO PEINING 3 EM 1</h3>
                    <p>Cabo multi-conector ultra resistente ideal para carregar múltiplos dispositivos.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('CABO PEINING 3 EM 1')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/RapidoCarregador.jpeg" alt="Carregador Turbo Rápido">
                    </div>
                    <h3>CARREGADOR TURBO RÁPIDO</h3>
                    <p>Fonte de alta amperagem desenvolvida para carregamento inteligente e estável.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('CARREGADOR TURBO RÁPIDO')">
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="card-produto">
                    <div class="img-container">
                        <img src="../imagens/USB-C 20W-PowerAdapter.jpeg" alt="Adaptador de Corrente 20W">
                    </div>
                    <h3>FONTE USB-C 20W PREMIUM</h3>
                    <p>Adaptador de tomada turbo de 20W perfeito para carregamento rápido de iPhones.</p>
                    <p class="preco">R$ 30,00</p>
                    <button onclick="verificarAcesso('FONTE USB-C 20W PREMIUM')">
                        Adicionar ao carrinho
                    </button>
                </div>

            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Oliver'CelL. Todos os direitos reservados.</p>
    </footer>

    <a href="https://wa.me/5561991857131" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script>
    function obterCarrinho() {
        return JSON.parse(localStorage.getItem('oliver_cart')) || [];
    }

    function atualizarContador() {
        const carrinho = obterCarrinho();
        const totalItens = carrinho.reduce((acc, item) => acc + item.qtd, 0);
        const contador = document.getElementById('cart-count');
        if(contador) {
            contador.innerText = totalItens;
        }
    }

    // Função de checagem corrigida: Injeta o comportamento do alerta sem quebras
   // Injeta o valor do PHP como um booleano real (true ou false) no JavaScript
const usuarioEstaLogado = <?php echo $usuarioLogado ? 'true' : 'false'; ?>;

function verificarAcesso(nomeProduto) {
    // Se o booleano for falso, barra na hora e mostra o alerta
    if (!usuarioEstaLogado) {
        alert("Para adicionar produtos ao carrinho e garantir os seus acessórios, você precisa fazer o login ou criar o seu cadastro primeiro!");
        window.location.href = window.location.origin + "/OliverCell/Clientes/Login.php";
        return; // Para a execução aqui
    }

    // Se estiver logado, continua para a função de adicionar
    executarAdicaoCarrinho(nomeProduto);
}

function ejecutarAdicaoCarrinho(nomeProduto) {
    let carrinho = obterCarrinho();
    let produtoExistente = carrinho.find(item => item.nome.toUpperCase() === nomeProduto.toUpperCase());

    let precoProduto = 0;
    let imagemProduto = "";
    
    const cards = document.querySelectorAll('.card-produto');
    cards.forEach(card => {
        const titulo = card.querySelector('h3').innerText.trim();
        if(titulo.toUpperCase() === nomeProduto.toUpperCase()) {
            const precoTexto = card.querySelector('.preco').innerText;
            precoProduto = parseFloat(precoTexto.replace('R$', '').replace(/\./g, '').replace(',', '.').trim()) || 0;
            imagemProduto = card.querySelector('.img-container img').getAttribute('src');
        }
    });

    if(produtoExistente) {
        produtoExistente.qtd += 1;
    } else {
        carrinho.push({ 
            nome: nomeProduto, 
            qtd: 1, 
            preco: precoProduto, 
            imagem: imagemProduto 
        });
    }

    localStorage.setItem('oliver_cart', JSON.stringify(carrinho));
    atualizarContador();
    alert(`${nomeProduto} foi adicionado ao seu carrinho!`);
}
    </script>
</body>
</html>