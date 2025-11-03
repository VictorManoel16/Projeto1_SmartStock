<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "projeto1";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão com o banco: " . $conn->connect_error);
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>
