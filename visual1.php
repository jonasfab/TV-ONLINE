<?php
include('./dados/db.php');

$mensagem = "";

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    echo "ID não especificado!";
    exit;
}

$user_id = $_GET['id'];

// Buscar dados
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Produto não encontrado!";
    exit;
}



?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV ONLINE</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <link rel="stylesheet" href="./visual01.css">
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="./dados/favicon.png" />

        <style>
        .logo a{
text-decoration: none;
color: white;
        }
    </style>
</head>

<body>

    <header>
        <div class="sub_header">
            <div class="logo"><span><a href="./index.php">TV ONLINE</a></span></div>

            <div class="hamburger" id="hamburger">
                <div class="bot_buscar2">
                    <a href="javascript:history.back()"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-arrow-left-square" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
                        </svg>VOLTAR</a>
                </div>
            </div>

            <form method="GET" id="menu" class="filtros">


                <div class="bot_buscar">
                    <a href="javascript:history.back()"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-arrow-left-square" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
                        </svg>VOLTAR</a>
                </div>
            </form>
        </div>
    </header>

    <section>


        <div class="titulo">
            <div class="sub_titulo01"></div>
            <p><?= $user['nome'] ?></p>
            <div class="sub_titulo02"></div>
            <br>
            <img src="./admin/<?= $user['imagem'] ?>" width="150" height="100" style="object-fit:cover; border-radius:5px;">
        </div>

        <!-- GRID -->
        <div class="grid">


            <!-- Create a video element -->
            <div class="video-box">
                <video id="video" controls></video>
            </div>


            <!-- Use hls.js to create a player and load the .m3u8 file -->
            <script>
                var video = document.getElementById('video');
                if (Hls.isSupported()) {
                    var hls = new Hls();
                    hls.loadSource('<?= $user['modelo'] ?>');
                    hls.attachMedia(video);
                    video.play();
                }
            </script>

            <?php if (!empty($mensagem)): ?>
                <div class="mensagem"><?= $mensagem ?></div>
            <?php endif; ?>

        </div>


    </section>

    <footer class="footer-content">

        <div class="footer-content02">
            &copy; <?= date('Y') ?> Desenvolvido por | <a href="../.././index.php">Jonas Fabricio</a>
        </div>
    </footer>



</body>

</html>