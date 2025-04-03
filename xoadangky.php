<?php
include 'db.php';

$MaSV = $_GET['masv'];
$MaHP = $_GET['mahp'];

$conn->query("DELETE FROM DangKy WHERE MaSV='$MaSV' AND MaHP='$MaHP'");

header("Location: hocphan.php");
?>
