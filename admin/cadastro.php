<?php
include('../dados/db.php');

$mensagem = "";

// Quando enviar o formulário
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];

    // Upload da imagem
    $imagem = "";

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {

        $ext = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $novoNome = uniqid() . "." . $ext;
        $caminho = "uploads/" . $novoNome;

        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho)) {
            $imagem = $caminho;
        } else {
            $mensagem = "Erro ao fazer upload da imagem!";
        }
    }

    // Inserir no banco
    $sql = "INSERT INTO produtos (nome, marca, modelo, descricao, imagem, tipo)
            VALUES ('$nome', '$marca', '$modelo', '$descricao', '$imagem', '$tipo')";

    if ($conn->query($sql)) {
        $mensagem = "Produto cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar: " . $conn->error;
    }
}
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

        form {
            background: #2a333b;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;

        }

        input,
        textarea,
        option,
        select {
            width: 100%;
            padding: 5px;
            margin-bottom: 15px;
            border-radius: 6px;
            background: #d4d4d4ff;
            font-size: 14px;
            border: 1px solid #aaa;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background: #fb7806;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        label {
            font-size: 14px;
            text-transform: uppercase;
        }

        .mensagem {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 300px;
            padding: 15px;
            color: #fff;
               background: #39ad35ff;
            border-radius: 5px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 0 10px #0003;
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
            <li><a href="controle.php">📊 Tabela</a></li>
            <li><a href="cadastro.php" class="activer">📁 Cadastros</a></li>
            <li><a href="../dados/logout.php">🚪 Sair</a></li>
        </ul>
    </div>

    <div class="content">


        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>NOME:</label>
            <input type="text" name="nome" required>

            <label>GÊNEROS:</label>
            <input type="text" name="marca" required>

            <label>URL:</label>
            <input type="text" name="modelo" required>

            <label>TIPO DE URL:</label>
            <select name="tipo">
                <option value="1">m3u8 - 01</option>
                <option value="3">m3u8 - 02</option>
                <option value="2">iframe</option>
            </select>


            <label>Descrição:</label>
            <textarea name="descricao" rows="4"></textarea>

            <label>Imagem:</label>
            <input type="file" name="imagem" accept="image/*" required>

            <button type="submit">Cadastrar</button>
        </form>

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