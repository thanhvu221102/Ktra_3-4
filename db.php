<?php
$host = "localhost";
$user = "root";  // Thay đổi nếu cần
$password = "";  // Thay đổi nếu có mật khẩu
$database = "test1";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
