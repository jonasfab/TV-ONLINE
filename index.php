<?php
include('./dados/db.php');

// ----- FILTROS -----
$pesquisa = $_GET['pesquisa'] ?? '';
$marca = $_GET['marca'] ?? '';
$modelo = $_GET['modelo'] ?? '';

// ---- PAGINAÇÃO ----
$por_pagina = 15;
$pagina = $_GET['pagina'] ?? 1;
$pagina = max(1, intval($pagina));
$offset = ($pagina - 1) * $por_pagina;


// -------- SQL BASE --------
$sql_base = "FROM produtos WHERE 1";

if ($pesquisa) {
    $sql_base .= " AND (nome LIKE '%$pesquisa%' 
                    OR descricao LIKE '%$pesquisa%' 
                    OR marca LIKE '%$pesquisa%'
                    OR modelo LIKE '%$pesquisa%')";
}

if ($marca)  $sql_base .= " AND marca = '$marca'";
if ($modelo) $sql_base .= " AND modelo = '$modelo'";


// -------- CONTAR TOTAL DE REGISTROS --------
$total = $conn->query("SELECT COUNT(*) AS total $sql_base")->fetch_assoc()['total'];
$paginas_totais = ceil($total / $por_pagina);


// -------- CONSULTA FINAL --------
$sql = "SELECT * $sql_base LIMIT $por_pagina OFFSET $offset";
$result = $conn->query($sql);


// ---- LISTAS DE MARCAS E MODELOS ----
$marcas = $conn->query("SELECT DISTINCT marca FROM produtos ORDER BY marca ASC");
$modelos = $conn->query("SELECT DISTINCT modelo FROM produtos ORDER BY modelo ASC");
?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV ONLINE</title>
    <link rel="stylesheet" href="./styles.css">
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="./dados/favicon.png" />

</head>

<body>

    <header>
        <div class="sub_header">
            <div class="logo"><span>TV ONLINE</span></div>

            <div class="hamburger" id="hamburger">☰</div>

            <form method="GET" id="menu" class="filtros">

                <input type="text" name="pesquisa" placeholder="BUSCAR..." value="<?= $pesquisa ?>">
                <a href="./index.php">INÍCIO</a>

                <select name="marca">
                    <option value="">GÊNEROS</option>
                    <?php while ($m = $marcas->fetch_assoc()): ?>
                        <option value="<?= $m['marca'] ?>" <?= $marca == $m['marca'] ? 'selected' : '' ?>>
                            <?= $m['marca'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>


                <div class="bot_buscar">
                    <button type="submit">BUSCAR</button>
                </div>
            </form>
        </div>
    </header>

    <section>

        <?php
        // ---- TÍTULO DINÂMICO ----
        if (!$marca && !$modelo) {
            $titulo = "Lista Completa (por popularidade)";
        } elseif ($marca && !$modelo) {
            $titulo = "Categoria: " . $marca;
        } elseif (!$marca && $modelo) {
            $titulo = "Categoria: " . $modelo;
        } else {
            $titulo = "Categoria: " . $marca . " / " . $modelo;
        }
        ?>

        <div class="titulo">
            <div class="sub_titulo01"></div>
            <p><?= $titulo ?></p>
            <div class="sub_titulo02"></div>
        </div>

        <!-- GRID -->
        <div class="grid">
            <?php if ($result->num_rows): ?>
                <?php while ($item = $result->fetch_assoc()):
                    $link_pagina = ($item['tipo'] == 1) ? "visual1.php" : "visual2.php";
                ?>
                    <div class="card">
                        <img src="./admin/<?= $item['imagem'] ?>" alt="<?= $item['nome'] ?>">
                        <h3 style=" text-transform: uppercase;"><?= $item['nome'] ?></h3>

                        <a href="<?= $link_pagina ?>?id=<?= $item['id'] ?>">Assistir</a>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <p>Nenhum resultado.</p>
            <?php endif; ?>
        </div>

        <?php if ($paginas_totais > 1): ?>

            <?php
            // Quantidade de páginas visíveis
            $max_links = 5;

            // Calcula início e fim
            $inicio = max(1, $pagina - floor($max_links / 2));
            $fim = min($paginas_totais, $inicio + $max_links - 1);

            // Ajustar caso esteja no final
            if (($fim - $inicio + 1) < $max_links) {
                $inicio = max(1, $fim - $max_links + 1);
            }

            // Função para montar URL com filtros
            function linkPag($p, $pesquisa, $marca, $modelo)
            {
                return "?pagina={$p}&pesquisa={$pesquisa}&marca={$marca}&modelo={$modelo}";
            }
            ?>

            <div style="text-align:center; padding:20px;">

                <!-- Botão primeira página -->
                <?php if ($pagina > 1): ?>
                    <a href="<?= linkPag(1, $pesquisa, $marca, $modelo) ?>"
                        style="padding:8px 12px; margin:4px; background:#444; color:white; border-radius:5px; text-decoration:none;">
                        ⏮
                    </a>
                <?php endif; ?>

                <!-- Botão página anterior -->
                <?php if ($pagina > 1): ?>
                    <a href="<?= linkPag($pagina - 1, $pesquisa, $marca, $modelo) ?>"
                        style="padding:8px 12px; margin:4px; background:#444; color:white; border-radius:5px; text-decoration:none;">
                        ◀
                    </a>
                <?php endif; ?>

                <!-- Números das páginas -->
                <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                    <a href="<?= linkPag($i, $pesquisa, $marca, $modelo) ?>"
                        style="
                padding: 8px 12px;
                margin: 4px;
                background: <?= $i == $pagina ? '#fb7806' : '#333' ?>;
                color: white;
                text-decoration:none;
                border-radius:5px;
               ">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Próxima página -->
                <?php if ($pagina < $paginas_totais): ?>
                    <a href="<?= linkPag($pagina + 1, $pesquisa, $marca, $modelo) ?>"
                        style="padding:8px 12px; margin:4px; background:#444; color:white; border-radius:5px; text-decoration:none;">
                        ▶
                    </a>
                <?php endif; ?>

                <!-- Última página -->
                <?php if ($pagina < $paginas_totais): ?>
                    <a href="<?= linkPag($paginas_totais, $pesquisa, $marca, $modelo) ?>"
                        style="padding:8px 12px; margin:4px; background:#444; color:white; border-radius:5px; text-decoration:none;">
                        ⏭
                    </a>
                <?php endif; ?>

            </div>

        <?php endif; ?>
    </section>

    <footer class="footer-content">
        <div class="footer-content01"><a href="./login.php">Login</a></div>
        <div class="footer-content02">
            &copy; <?= date('Y') ?> Desenvolvido por | <a href="../.././index.php">Jonas Fabricio</a>
        </div>
    </footer>

    <script>
        document.getElementById('hamburger').onclick = () => {
            const menu = document.getElementById('menu');
            menu.style.display = menu.style.display === "flex" ? "none" : "flex";
        };
    </script>

</body>

</html>