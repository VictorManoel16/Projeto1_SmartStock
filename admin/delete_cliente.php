<?php
include_once("config.inc.php");
session_start();
if (!isset($_SESSION['admin_id'])) redirect("login.php");

$id = intval($_GET['id'] ?? 0);
if ($id>0) {
    $stmt = $conn->prepare("DELETE FROM clientes WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}
redirect("admin_clientes.php");
?>
