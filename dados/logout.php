<?php

session_start();
include('./db.php');

// Verificar se o usuário está logado
if (isset($_SESSION['user_id'])) {
    // Obter o ID do usuário da sessão
    $user_id = $_SESSION['user_id'];

    // Atualizar o status para 'deslogado'
    $sql_update = "UPDATE usuarios SET status = 'deslogado' WHERE id = $user_id";
    $conn->query($sql_update);

    // Destruir a sessão
    session_unset();  // Remove todas as variáveis da sessão
    session_destroy();  // Destroi a sessão

    // Redirecionar para a página de login ou outra página
    header('Location: ../index.php'); // Redireciona para a página de login
    exit;
} else {
    // Se não houver sessão ativa, redireciona para a página inicial
    header('Location: ../index.php');
    exit;
}
