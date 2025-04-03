<?php
include 'db.php';

$MaSV = $_GET['masv'];
$MaHP = $_GET['mahp'];

// Kiểm tra xem sinh viên đã đăng ký chưa
$check = $conn->query("SELECT * FROM DangKy WHERE MaSV='$MaSV' AND MaHP='$MaHP'");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO DangKy (MaSV, MaHP) VALUES ('$MaSV', '$MaHP')");
}

header("Location: hocphan.php");
?>
