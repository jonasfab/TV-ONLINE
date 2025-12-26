
<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4"); // Define a conexão para UTF-8

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
