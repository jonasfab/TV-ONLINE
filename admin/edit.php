<?php
include('../dados/db.php');

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

// ----------- EXCLUIR -----------
if (isset($_POST['acao']) && $_POST['acao'] == "excluir") {

    // excluir imagem (somente se existir e for arquivo)
    if (!empty($user['imagem']) && file_exists($user['imagem']) && is_file($user['imagem'])) {
        unlink($user['imagem']);
    }

    $sqlDel = "DELETE FROM produtos WHERE id = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param("i", $user_id);

    if ($stmtDel->execute()) {
        header("Location: controle.php?msg=excluido");
        exit;
    } else {
        $mensagem = "Erro ao excluir!";
    }
}

// ----------- EDITAR -----------
if (isset($_POST['acao']) && $_POST['acao'] == "editar") {

    $nome = $_POST['nome'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];
    $imagem = $user['imagem'];

    // Upload nova imagem
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {

        $ext = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $novoNome = uniqid() . "." . $ext;
        $caminho = "uploads/" . $novoNome;

        // *** CORREÇÃO IMPORTANTE: faltava parêntese ***
        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho)) {

            // APAGAR IMAGEM ANTIGA
            if (!empty($user['imagem']) && file_exists($user['imagem']) && is_file($user['imagem'])) {
                unlink($user['imagem']);
            }

            // Salva nova imagem
            $imagem = $caminho;
        }
    }

    $sql = "UPDATE produtos 
            SET nome=?, marca=?, modelo=?, descricao=?, tipo=?, imagem=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nome, $marca, $modelo, $descricao, $tipo, $imagem, $user_id);

    if ($stmt->execute()) {
        $mensagem = "Produto atualizado com sucesso!";

        // Atualiza dados na tela sem reload
        $user['nome'] = $nome;
        $user['marca'] = $marca;
        $user['modelo'] = $modelo;
        $user['descricao'] = $descricao;
        $user['tipo'] = $tipo;
        $user['imagem'] = $imagem;
    } else {
        $mensagem = "Erro ao atualizar!";
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
        .imagem {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }

        /* Modal */
        .modal{
               background: #2a333b;
        }
        .modal-bg {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            position: fixed;
            top: 0;
            left: 0;
            display: none;
            justify-content: center;
            align-items: center;
            color: #5c5c5cff;
        }

        .modal-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
        }

        .modal-btn {
            margin: 10px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm {
            background: #28a745;
            color: white;
        }

        .cancel {
            background: #6c757d;
            color: white;
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
            <li><a href="controle.php" class="activer">🚪 Voltar</a></li>
        </ul>
    </div>

    <div class="content">


        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Nome</label>
            <input type="text" name="nome" required value="<?= htmlspecialchars($user['nome']) ?>">

            <label>Marca</label>
            <input type="text" name="marca" value="<?= htmlspecialchars($user['marca']) ?>">

            <label>Modelo</label>
            <input type="text" name="modelo" value="<?= htmlspecialchars($user['modelo']) ?>">

            <label>Descrição</label>
            <textarea name="descricao" rows="4"><?= htmlspecialchars($user['descricao']) ?></textarea>

            <label>Tipo</label>
            <select name="tipo">
                <option value="1" <?= $user['tipo'] == '1' ? 'selected' : '' ?>>m3u8 - 01</option>
                <option value="3" <?= $user['tipo'] == '3' ? 'selected' : '' ?>>m3u8 - 02</option>
                <option value="2" <?= $user['tipo'] == '2' ? 'selected' : '' ?>>iframe</option>
            </select>

            <label>Imagem Atual:</label>
            <div class="imagem">
                <img src="<?= $user['imagem'] ?>" width="220" height="150" style="object-fit:cover; border-radius:5px;">
            </div>

            <label>Nova imagem</label>
            <input type="file" name="imagem" accept="image/*">

            <br><br>

            <button type="button" class="btn-edit" onclick="abrirModal('editar')">Salvar</button>
            <button type="button" class="btn-delete" onclick="abrirModal('excluir')">Excluir</button>

            <!-- Modal -->
            <div class="modal-bg" id="modal">
                <div class="modal-box">
                    <h3>Confirmar ação?</h3>
                    <p>Tem certeza que deseja continuar?</p>

                    <input type="hidden" name="acao" id="modalAcao">

                    <button type="submit" class="modal-btn confirm">Sim</button>
                    <button type="button" class="modal-btn cancel" onclick="fecharModal()">Cancelar</button>
                </div>
            </div>

        </form>

    </div>

    <script>
        function abrirModal(tipo) {
            document.getElementById("modalAcao").value = tipo;
            document.getElementById("modal").style.display = "flex";
        }

        function fecharModal() {
            document.getElementById("modal").style.display = "none";
        }
    </script>

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