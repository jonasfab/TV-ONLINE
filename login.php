<?php
session_start();
include('./dados/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$email = trim($_POST['email']);  // Remove espaços extras
	$senha = $_POST['senha'];

	// Buscar usuário pelo e-mail exatamente como no banco
	$sql = "SELECT * FROM usuarios WHERE BINARY email = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$usuario = $result->fetch_assoc();

		// Verifica se o usuário está ativo
		if ($usuario['ativo'] == 0) {
			$mensagem = "E-mail desativado! Entre em contato com o administrador.";
		} elseif (password_verify($senha, $usuario['senha'])) {

			// Iniciar sessão e redirecionar para a página principal
			$_SESSION['user_id'] = $usuario['id'];
			$_SESSION['username'] = $usuario['nome'];
			$_SESSION['email'] = $usuario['email'];
			$_SESSION['data_criacao'] = $usuario['data_criacao'];
			$_SESSION['telefone'] = $usuario['telefone'];
			$_SESSION['acesso_admin'] = $usuario['acesso_admin'];
			$_SESSION['acesso_conteudo_1'] = $usuario['acesso_conteudo_1'];
			$_SESSION['acesso_conteudo_2'] = $usuario['acesso_conteudo_2'];
			$_SESSION['acesso_conteudo_3'] = $usuario['acesso_conteudo_3'];
			$_SESSION['acesso_conteudo_4'] = $usuario['acesso_conteudo_4'];
			$_SESSION['acesso_conteudo_5'] = $usuario['acesso_conteudo_5'];

			// Atualizar último login e status
			$sql_update = "UPDATE usuarios SET ultimo_login = NOW(), status = 'logado' WHERE id = ?";
			$stmt_update = $conn->prepare($sql_update);
			$stmt_update->bind_param('i', $usuario['id']);
			$stmt_update->execute();

			header('Location: ./admin/controle.php');
			exit;
		} else {
			$mensagem = "Senha incorreta!";
		}
	} else {
		$mensagem = "E-mail não encontrado!";
	}
}

// Mensagens de sucesso ou erro da sessão
if (isset($_SESSION['messagesSucesso'])) {
	$mensagem2 = $_SESSION['messagesSucesso'];
	unset($_SESSION['messagesSucesso']);
}

if (isset($_SESSION['messageErro'])) {
	$mensagem2 = $_SESSION['messageErro'];
	unset($_SESSION['messageErro']);
}


?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV ONLINE</title>
    <link rel="stylesheet" href="./visual01.css">
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="./dados/favicon.png" />

    <style>
        /* LOGIN BOX */
        .login-box {
            width: 100%;
            max-width: 380px;
            background: #2d3740;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px #00000055;
            text-align: center;
        }


        .login-box form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .login-box input {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #1a1f24;
            color: #fff;
            font-size: 15px;
        }

        .login-box input::placeholder {
            color: #aaa;
        }

        label {
            text-align: left;
        }

        .login-box button {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #fb7806;
            cursor: pointer;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
        }

        .login-box button:hover {
            background: #ff8f30;
        }

        /* Ajuste para centralização perfeita dentro do grid */
        .grid {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .mensagem-fixa {
			position: fixed;
			bottom: 10px;
			right: 10px;
			width: 300px;
			padding: 15px;
			color: #fff;
			border-radius: 5px;
			font-size: 14px;
			z-index: 1000;
			box-shadow: 0 0 10px #0003;
		}
    </style>
</head>

<body>
	<?php if (!empty($mensagem)): ?>
		<div id="sos" style="background-color:#ff8f30;" class="mensagem-fixa">
			<?php echo $mensagem; ?>
		</div>
	<?php endif; ?>

	<?php if (!empty($mensagem2)): ?>
		<div id="sos" style="background-color:#ff8f30;" class="mensagem-fixa">
			<?php echo $mensagem2; ?>
		</div>
	<?php endif; ?>

    <header>
        <div class="sub_header">
            <div class="logo"><span>TV ONLINE</span></div>

            <div class="hamburger" id="hamburger">
                <div class="bot_buscar2">
                    <a href="./index.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-arrow-left-square" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
                        </svg>VOLTAR</a>
                </div>
            </div>

            <form method="GET" id="menu" class="filtros">


                <div class="bot_buscar">
                    <a href="./index.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
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
            <p>Login - Painel Admin</p>
            <div class="sub_titulo02"></div>
            <br>

        </div>

        <div class="grid">

            <div class="login-box">

                <form  method="POST">

                    <label for="email">E-mail:</label>
                    <input type="email" name="email" required placeholder="Digite seu e-mail">

                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" required placeholder="Digite sua senha">

                    <button type="submit">Acessar</button>
                </form>
            </div>

        </div>


    </section>

    <footer class="footer-content">

        <div class="footer-content02">
            &copy; <?= date('Y') ?> Desenvolvido por | <a href="../.././index.php">Jonas Fabricio</a>
        </div>
    </footer>

<!-- Funções -->
	<script>
		function converterMinusculas() {
			let input = document.getElementById("meuInput");
			input.value = input.value.toLowerCase();
		}

		setTimeout(() => {
			let mensagem = document.getElementById("sos");
			if (mensagem) mensagem.style.display = "none";
		}, 10000);

		$(window).scroll(function() {
			if ($(this).scrollTop() > 60) {
				$(".scroll-aparecer").css({
					opacity: "1",
					height: "60px"
				});
			} else {
				$(".scroll-aparecer").css({
					opacity: "0",
					height: "0"
				});
			}
		});
	</script>
</body>

</html>