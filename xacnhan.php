<?php
include 'db.php';

session_start();

if (!isset($_SESSION["MaSV"])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION["MaSV"];

// Lấy thông tin sinh viên từ database
$sql_sv = "SELECT MaSV, HoTen, GioiTinh, NgaySinh, MaNganh FROM SinhVien WHERE MaSV = '$MaSV'";
$result_sv = $conn->query($sql_sv);
$SinhVien = $result_sv->fetch_assoc();

// Kiểm tra giỏ hàng có dữ liệu không
if (empty($_SESSION["cart"])) {
    header("Location: giohang.php");
    exit();
}

// Xử lý lưu vào database khi sinh viên bấm "Xác nhận"
if (isset($_POST["confirm"])) {
    // Kiểm tra xem sinh viên đã có mã đăng ký chưa
    $sql_check_dk = "SELECT MaDK FROM DangKy WHERE MaSV = '$MaSV'";
    $result_check_dk = $conn->query($sql_check_dk);

    if ($result_check_dk->num_rows > 0) {
        // Nếu đã có, lấy mã đăng ký cũ
        $row_dk = $result_check_dk->fetch_assoc();
        $MaDK = $row_dk["MaDK"];
    } else {
        // Nếu chưa có, tạo mới
        $sql_dk = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), '$MaSV')";
        $conn->query($sql_dk);
        $MaDK = $conn->insert_id;
    }

    // Thêm vào bảng ChiTietDangKy
    foreach ($_SESSION["cart"] as $MaHP) {
        // Kiểm tra số lượng còn lại của học phần
        $sql_check = "SELECT SoLuong FROM HocPhan WHERE MaHP = '$MaHP'";
        $result_check = $conn->query($sql_check);
        $row = $result_check->fetch_assoc();

        if ($row["SoLuong"] > 0) {
            // Giảm số lượng học phần
            $sql_update = "UPDATE HocPhan SET SoLuong = SoLuong - 1 WHERE MaHP = '$MaHP'";
            $conn->query($sql_update);

            // Kiểm tra xem đã đăng ký môn này chưa để tránh trùng lặp
            $sql_check_ctdk = "SELECT * FROM ChiTietDangKy WHERE MaDK = '$MaDK' AND MaHP = '$MaHP'";
            $result_check_ctdk = $conn->query($sql_check_ctdk);

            if ($result_check_ctdk->num_rows == 0) {
                // Nếu chưa đăng ký, thêm vào bảng ChiTietDangKy
                $sql_ctdk = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES ('$MaDK', '$MaHP')";
                $conn->query($sql_ctdk);
            }
        }
    }
    $_SESSION["cart"] = [];
    $message = "✅ Đăng ký thành công!";
}

// Lấy danh sách học phần trong giỏ hàng
$hocPhanList = [];
$totalCredits = 0;

if (count($_SESSION["cart"]) > 0) {
    $MaHP_list = "'" . implode("','", $_SESSION["cart"]) . "'";
    $result_hp = $conn->query("SELECT * FROM HocPhan WHERE MaHP IN ($MaHP_list)");

    while ($row = $result_hp->fetch_assoc()) {
        $hocPhanList[] = $row;
        $totalCredits += $row["SoTinChi"];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Đăng Ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        h2,
        h3 {
            color: #333;
        }

        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #007BFF;
            color: white;
        }

        td {
            background: #f9f9f9;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 5px;
        }

        button:hover {
            background: #218838;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h2>✅ Xác Nhận Đăng Ký</h2>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <h3>📌 Thông tin sinh viên</h3>
    <table border="1">
        <tr>
            <td><strong>Mã SV:</strong></td>
            <td><?= $SinhVien["MaSV"] ?></td>
        </tr>
        <tr>
            <td><strong>Họ tên:</strong></td>
            <td><?= $SinhVien["HoTen"] ?></td>
        </tr>
        <tr>
            <td><strong>Giới Tính:</strong></td>
            <td><?= $SinhVien["GioiTinh"] ?></td>
        </tr>
        <tr>
            <td><strong>MaNganh:</strong></td>
            <td><?= $SinhVien["MaNganh"] ?></td>
        </tr>
    </table>

    <h3>📌 Học phần đã chọn</h3>
    <table border="1">
        <tr>
            <th>Mã Học Phần</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
        </tr>
        <?php foreach ($hocPhanList as $hp): ?>
            <tr>
                <td><?= $hp["MaHP"] ?></td>
                <td><?= $hp["TenHP"] ?></td>
                <td><?= $hp["SoTinChi"] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>📌 Tổng số học phần:</strong> <?= count($hocPhanList) ?></p>
    <p><strong>📌 Tổng số tín chỉ:</strong> <?= $totalCredits ?></p>

    <form method="POST">
        <button type="submit" name="confirm">✅ Xác Nhận Đăng Ký</button>
    </form>

    <a href="giohang.php">🔙 Quay lại Giỏ Hàng</a>
</body>

</html>