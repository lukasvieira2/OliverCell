<?php
session_start();

// Inclusão da conexão com o banco de dados
include_once('../../config.php');

// Verifica se o usuário comum está logado no sistema
$usuarioLogado = (isset($_SESSION['usuario_email']) && !empty($_SESSION['usuario_email'])) ? true : false;

// Puxa os produtos do banco com o estoque em tempo real
$query_produtos = "SELECT * FROM produtos"; 
$resultado_produtos = mysqli_query($conexao, $query_produtos);

// Cria um mapa/array contendo o estoque de cada produto associado ao seu nome exato
$estoque_real = [];
if ($resultado_produtos) {
    while($prod = mysqli_fetch_assoc($resultado_produtos)) {
        $estoque_real[strtoupper($prod['nome'])] = (int)$prod['estoque'];
    }
}

// Função auxiliar para verificar e retornar o estoque de um produto de forma segura
function pegarEstoque($nome, $mapaEstoque) {
    $nomeChave = strtoupper($nome);
    return isset($mapaEstoque[$nomeChave]) ? $mapaEstoque[$nomeChave] : 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oliver'CelL - Acessórios Premium</title>
    <link rel="icon" href="../../imagens/logo.png" type="image/png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../css/acessorios.css">
    <link rel="stylesheet" href="../../css/header.css">
</head>
<body>

    <header class="header">
        <div class="header-container">
            <a href="../cliente.html" class="logo-link">
                <img src="../../imagens/logo.png" alt="Logo" class="logo-img">
                <span class="logo-text">Oliver'<span>CelL</span></span>
            </a>
        
            <nav class="nav-principal">
                <ul class="menu-main">
                    <li><a href="../cliente.html">Início</a></li>
                    <li><a href="servicos.html">Serviços</a></li>
                    <li><a href="contato.html">Contato</a></li>
                    <li>
                        <a href="../Perfil.php" class="btn-login">
                            <i class="fas fa-user-circle"></i> PERFIL
                        </a>
                    </li>
                    <li>
                        <a href="../carrinho.php" style="position: relative;">
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
                
                <?php $nomeP = 'CABO TIPO-C PREMIUM'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/CaboCelularTipoC.jpg" alt="Cabo Tipo C">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo de alta velocidade e durabilidade Masterdrive para conexões Tipo-C.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'CABO LIGHTNING IPHONE'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/cabocelularlightning .png" alt="Cabo Lightning">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo Masterdrive premium ideal para carga rápida e segura no seu iPhone.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'FONE ESTÉREO BRANCO'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/foneBranco.png" alt="Fone Branco">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Som Premium balanceado com super bass da Alpha Gold na cor branca.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'FONE ESTÉREO PRETO'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/fonePreto.png" alt="Fone Preto">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fone Alpha Gold de ouvido estéreo com excelente isolamento e graves profundos.</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'SUPORTE VEICULAR 360°'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/CarMountAirVentHolder.png" alt="Suporte Veicular">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Suporte Peining articulado com rotação completa para fixação estável no painel.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'CARREGADOR INOVA CA41'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/CA41.jpeg" alt="Carregador Inova CA41">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Carregador rápido e seguro de alta performance para o dia a dia.</p>
                    <p class="preco">R$ 20,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'FONE BLUETOOTH TWS'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/FoneBluetooth.jpeg" alt="Fone Bluetooth Premium">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fone de ouvido sem fio de alta fidelidade com excelente autonomia de bateria.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'CABO PEINING 3 EM 1'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/Pei-185-3.jpeg" alt="Cabo Peining 3 em 1">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo multi-conector ultra resistente ideal para carregar múltiplos dispositivos.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'CARREGADOR TURBO RÁPIDO'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/RapidoCarregador.jpeg" alt="Carregador Turbo Rápido">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fonte de alta amperagem desenvolvida para carregamento inteligente e estável.</p>
                    <p class="preco">R$ 40,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = 'FONTE USB-C 20W PREMIUM'; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/USB-C 20W-PowerAdapter.jpeg" alt="Adaptador de Corrente 20W">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Adaptador de tomada turbo de 20W perfeito para carregamento rápido de iPhones.</p>
                    <p class="preco">R$ 30,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>


                <?php $nomeP = "CABO TURBO TYPE-C ALPHA GOLD 4.8A"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/lkjjkl.jpeg" alt="Cabo Tipo C Alpha Gold">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo de dados de alta velocidade 4.8A com chip inteligente e acabamento reforçado.</p>
                    <p class="preco">R$ 20,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CABO TIPO-C PARA LIGHTNING PD 30W"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/q.jpeg" alt="Cabo Tipo C para Lightning">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo Alpha Gold Pro de alta performance 30W ideal para carga rápida em iPhones.</p>
                    <p class="preco">R$ 25,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CABO TIPO-C PARA TIPO-C PD 60W"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/qwe.jpeg" alt="Cabo Tipo C para Tipo C">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo Alpha Gold Pro Power Delivery de 60W, perfeito para novos smartphones e notebooks.</p>
                    <p class="preco">R$ 30,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CABO MICRO-USB V8 FAST CHARGE 3A"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/daz.jpeg" alt="Cabo Micro USB V8">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo de carregamento rápido 3A Relog's com transmissão estável de dados (1 metro).</p>
                    <p class="preco">R$ 15,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CABO TYPE-C PARA TYPE-C APPLE 1M"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/jklhf.jpeg" alt="Cabo Apple Tipo C">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Cabo de carga original Apple (1 metro) com conectores Tipo-C em ambas as pontas.</p>
                    <p class="preco">R$ 45,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CARREGADOR TURBO H'MASTON 4.1A Y18-1"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/kl.jpeg" alt="Carregador H'Maston Y18-1">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Carregador de parede ultra rápido homologado pela ANATEL com cabo incluso de 1 metro.</p>
                    <p class="preco">R$ 35,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CARREGADOR DUPLO USB H'MASTON 3.1A"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/lk.jpeg" alt="Carregador Duplo H'Maston">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fonte de tomada com saídas duplas USB de 3.1A para carregar dois aparelhos ao mesmo tempo.</p>
                    <p class="preco">R$ 30,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CARREGADOR VEICULAR DUAS USB 3.1A"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/2usb.jpeg" alt="Carregador Veicular H'Maston">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Carregador veicular de alta eficiência com saídas USB duplas e cabo iOS incluso.</p>
                    <p class="preco">R$ 25,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CARREGADOR VEICULAR TURBO 4.1A"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/oi.jpeg" alt="Carregador Veicular Turbo">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fonte automotiva premium homologada pela ANATEL para carregamento rápido e seguro em viagens.</p>
                    <p class="preco">R$ 30,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>


                <?php $nomeP = "FONE BLUETOOTH TWS I12 BRANCO"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/blue.jpeg" alt="Fone Bluetooth i12 Branco">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fone de ouvido sem fio i12 estéreo com conexão Bluetooth estável e pareamento automático.</p>
                    <p class="preco">R$ 35,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "FONE BLUETOOTH AIRDOTS COM DISPLAY"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/ouvido2.jpeg" alt="Fone Bluetooth AirDots">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Fone AirDots sem fio V5.1 com estojo de carregamento inteligente e indicador de bateria em LED.</p>
                    <p class="preco">R$ 45,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
                    </button>
                </div>

                <?php $nomeP = "CAIXA DE SOM DUPLO SEM FIO TN18"; $est = pegarEstoque($nomeP, $estoque_real); ?>
                <div class="card-produto">
                    <div class="img-container">
                        <img src="../../imagens/son.jpeg" alt="Caixa de Som H'Maston TN18">
                    </div>
                    <h3><?php echo $nomeP; ?></h3>
                    <p>Caixa de som Bluetooth portátil H'Maston TN18 com graves potentes e iluminação dinâmica em LED RGB.</p>
                    <p class="preco">R$ 55,00</p>
                    <button onclick="verificarAcesso('<?php echo $nomeP; ?>')" <?php echo ($est <= 0) ? 'disabled style="background:#555; cursor:not-allowed;"' : ''; ?>>
                        <?php echo ($est <= 0) ? 'Esgotado' : 'Adicionar ao carrinho'; ?>
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
        const estoqueDisponivel = <?php echo json_encode($estoque_real); ?>;
        const usuarioEstaLogado = <?php echo $usuarioLogado ? 'true' : 'false'; ?>;

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

        function verificarAcesso(nomeProduto) {
            if (!usuarioEstaLogado) {
                alert("Para adicionar produtos ao carrinho e garantir os seus acessórios, você precisa fazer o login ou criar o seu cadastro primeiro!");
                window.location.href = window.location.origin + "/OliverCell/Clientes/Login.php";
                return;
            }

            const carrinho = obterCarrinho();
            const produtoNoCarrinho = carrinho.find(item => item.nome.toUpperCase() === nomeProduto.toUpperCase());
            const quantidadeAtualNoCarrinho = produtoNoCarrinho ? produtoNoCarrinho.qtd : 0;
            
            const chaveProduto = nomeProduto.toUpperCase();
            const estoqueLimite = estoqueDisponivel[chaveProduto] !== undefined ? estoqueDisponivel[chaveProduto] : 0;

            if (quantidadeAtualNoCarrinho >= estoqueLimite) {
                alert(`Desculpe! No momento só temos ${estoqueLimite} unidade(s) de "${nomeProduto}" em estoque e você já atingiu o limite no seu carrinho.`);
                return;
            }

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
                    precoProduto = parseFloat(precoTexto.replace('R$', '').replace('.', '').replace(',', '.').trim());
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

        atualizarContador();
    </script>
</body>
</html>