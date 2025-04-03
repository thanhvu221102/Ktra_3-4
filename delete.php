<?php
include 'db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM SinhVien WHERE MaSV='$id'");

header("Location: index.php");
?>
