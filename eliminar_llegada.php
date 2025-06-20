<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: home.php");
    exit();
}
require_once "conn/conexion.php";

$id = $_GET['id'] ?? 0;

$sql = "DELETE FROM llegadas_tarde WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);

header("Location: latepass.php");
exit();
?>