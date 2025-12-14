<?php
include('../dados/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// ---- PAGINAÇÃO ----
$por_pagina = 15;
$pagina = $_GET['pagina'] ?? 1;
$pagina = max(1, intval($pagina));
$offset = ($pagina - 1) * $por_pagina;

// Contar total de registros
$total = $conn->query("SELECT COUNT(*) AS total FROM produtos")->fetch_assoc()['total'];
$paginas_totais = ceil($total / $por_pagina);

// Buscar dados
$sql = "SELECT * FROM produtos ORDER BY id DESC LIMIT $por_pagina OFFSET $offset";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <link rel="icon" type="image/x-icon" href="../dados/favicon.png" />

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #1b2127;
            color: #fff;
        }

        .sidebar {
            position: fixed;
            left: 0;

            top: 0;
            width: 200px;
            height: 100%;
            background: #1e262d;
            padding: 20px;
            transform: translateX(-260px);
            transition: 0.3s;
            z-index: 999;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-btn {
            font-size: 28px;
            cursor: pointer;
            color: #fb7806;
            display: none;
        }

        @media(max-width: 1000px) {
            .close-btn {
                display: block;
            }
        }

        .menu-list {
            margin-top: 20px;
            list-style: none;
            padding: 0;
        }

        .menu-list a {
            color: #fff;
            text-decoration: none;
            padding: 12px;
            display: block;
            background: #2a333b;
            border-radius: 8px;
            transition: 0.3s;
            margin-bottom: 10px;
        }

        .menu-list a:hover,
        .activer {
            background: #fb7806 !important;
        }

        .activer:hover {
            background: #b95f10ff !important;
        }

        .open-sidebar-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            background: #fb7806;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            color: #fff;
            font-size: 24px;
            z-index: 998;
        }

        .content {
            padding: 30px;
            margin-top: 40px;
        }

        @media(min-width: 1000px) {
            .sidebar {
                transform: translateX(0);
            }

            .open-sidebar-btn {
                display: none;
            }

            .content {
                margin-left: 260px;
            }
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #2a333b;
            padding: 6px;
            font-size: 13px;
            white-space: nowrap;
            text-align: left;
            color: #dddcdcff;
        }

        tbody tr:hover {
            background-color: #3f4e5cff;
        }

        tbody a {
            text-decoration: none;
            color: #fb7806;
            background-color: #2a333b;
            padding: 4px 8px;
            border-radius: 5px;
        }

        tbody a:hover {
            background-color: #fb7806;
            color: #1b2127;

        }

        th {
    text-transform: uppercase;
            background-color: #2a333b;
        }
    </style>

</head>

<body>

    <div class="open-sidebar-btn" id="openSidebar">&#9776;</div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2 style="color:#fb7806;">Painel Admin</h2>
            <span class="close-btn" id="closeSidebar">&times;</span>
        </div>

        <ul class="menu-list">
            <li><a href="controle.php" class="activer">📊 Tabela</a></li>
            <li><a href="cadastro.php">📁 Cadastros</a></li>
            <li><a href="../dados/logout.php">🚪 Sair</a></li>
        </ul>
    </div>

    <div class="content">


        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ações</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Gêneros</th>
                        <th>URL</th>
                        <th>TIPO DE URL</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $result->fetch_assoc()): ?>
                        <tr>
                            <td><a href="./edit.php?id=<?= $c['id'] ?>">Editar</a></td>
                            <td><img src="<?= $c['imagem'] ?>" width="100" height="60" style="object-fit:cover; border-radius:5px;"></td>
                            <td><?= $c['nome'] ?></td>
                            <td><?= $c['marca'] ?></td>
                            <td><?= $c['modelo'] ?></td>
                            <td>
                                <?= ($c['tipo'] == 1 ? 'm3u8 - 01' : ($c['tipo'] == 3 ? 'm3u8 - 02' : ($c['tipo'] == 2 ? 'iframe' : ''))) ?>
                            </td>
                            <td><?= $c['descricao'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINAÇÃO -->
        <?php if ($paginas_totais > 1): ?>

            <?php
            $max_links = 5;
            $inicio = max(1, $pagina - floor($max_links / 2));
            $fim = min($paginas_totais, $inicio + $max_links - 1);

            if (($fim - $inicio + 1) < $max_links) {
                $inicio = max(1, $fim - $max_links + 1);
            }

            function linkPag($p)
            {
                return "?pagina={$p}";
            }
            ?>

            <div style="text-align:center; padding:20px;">

                <?php if ($pagina > 1): ?>
                    <a href="<?= linkPag(1) ?>" style="padding:8px 12px; background:#444; color:#fff; border-radius:5px;">⏮</a>
                    <a href="<?= linkPag($pagina - 1) ?>" style="padding:8px 12px; background:#444; color:#fff; border-radius:5px;">◀</a>
                <?php endif; ?>

                <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                    <a href="<?= linkPag($i) ?>"
                        style="padding:8px 12px; margin:4px; background:<?= $i == $pagina ? '#fb7806' : '#333' ?>; color:white; border-radius:5px;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina < $paginas_totais): ?>
                    <a href="<?= linkPag($pagina + 1) ?>" style="padding:8px 12px; background:#444; color:#fff; border-radius:5px;">▶</a>
                    <a href="<?= linkPag($paginas_totais) ?>" style="padding:8px 12px; background:#444; color:#fff; border-radius:5px;">⏭</a>
                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

    <script>
        let sidebar = document.getElementById("sidebar");
        let openBtn = document.getElementById("openSidebar");
        let closeBtn = document.getElementById("closeSidebar");

        openBtn.onclick = () => sidebar.classList.add("open");
        closeBtn.onclick = () => sidebar.classList.remove("open");

        document.addEventListener("click", function(e) {
            if (window.innerWidth <= 1000) {
                if (!sidebar.contains(e.target) && !openBtn.contains(e.target)) {
                    sidebar.classList.remove("open");
                }
            }
        });
    </script>

</body>

</html>