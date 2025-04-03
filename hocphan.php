<?php
include 'db.php';
session_start();

if (!isset($_SESSION["MaSV"])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION["MaSV"];

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$message = "";
$messageClass = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["MaHP"])) {
    $MaHP = $_POST["MaHP"];

    $sql_check = "SELECT * FROM ChiTietDangKy 
                  JOIN DangKy ON ChiTietDangKy.MaDK = DangKy.MaDK 
                  WHERE DangKy.MaSV = '$MaSV' AND ChiTietDangKy.MaHP = '$MaHP'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $message = "⚠ Bạn đã đăng ký học phần này rồi!";
        $messageClass = "";
    } elseif (!in_array($MaHP, $_SESSION["cart"])) {
        $_SESSION["cart"][] = $MaHP;
        $message = "✅ Đã thêm vào giỏ hàng!";
        $messageClass = "success";
    }
}

$result_hp = $conn->query("SELECT * FROM HocPhan");

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Học Phần</title>
    <style>
        body {
            font-family: 'Nunito', 'Segoe UI', sans-serif;
            background: #f0f7ff;
            background-image: linear-gradient(135deg, #f0f7ff 0%, #e4f1ff 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            color: #2d3748;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 0;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            position: relative;
        }

        .header {
            background: #00b894;
            padding: 25px 30px;
            color: white;
            position: relative;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background-image: linear-gradient(to right, #00b894, #00cec9);
        }

        h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
        }

        .message {
            background: #fff8e6;
            border-left: 4px solid #ffc107;
            padding: 12px 20px;
            margin: 20px 30px;
            color: #856404;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .message.success {
            background: #e6fff8;
            border-left: 4px solid #00b894;
            color: #00785a;
        }

        .table-container {
            padding: 20px 30px;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #edf2f7;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #edf2f7;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .table tr:hover {
            background-color: #f7fafc;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .btn {
            background: #00b894;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            background: #00a382;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .links {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 30px;
            background: #f7fafc;
            border-top: 1px solid #edf2f7;
        }

        .btn-cart,
        .btn-logout,
        .btn-backhome {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-cart {
            background: #00b894;
            color: white;
        }

        .btn-cart:hover {
            background: #00a382;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .btn-backhome {
            background: transparent;
            color: #4a5568;
            border: 1px solid #cbd5e0;
        }

        .btn-backhome:hover {
            background: #f7fafc;
            transform: translateY(-1px);
        }

        .btn-logout {
            background: transparent;
            color: #e53e3e;
            border: 1px solid #e53e3e;
        }

        .btn-logout:hover {
            background: #fff5f5;
            transform: translateY(-1px);
        }

        .credit-badge {
            display: inline-block;
            background: #e6fff8;
            color: #00b894;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .quantity-badge {
            display: inline-block;
            background: #ebf4ff;
            color: #4299e1;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            
            .header {
                padding: 20px;
            }
            
            .table-container {
                padding: 15px;
            }
            
            .links {
                flex-direction: column;
                padding: 15px;
            }
            
            .btn-cart, .btn-logout, .btn-backhome {
                width: 100%;
                justify-content: center;
                margin-bottom: 10px;
            }
            
            .table th, .table td {
                padding: 10px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>📚 Danh Sách Học Phần</h2>
        </div>

        <?php if ($message): ?>
            <div class="message <?= $messageClass ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <form method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã Học Phần</th>
                            <th>Tên Học Phần</th>
                            <th>Số Tín Chỉ</th>
                            <th>Số Lượng</th>
                            <th>Đăng Ký</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_hp->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row["MaHP"] ?></td>
                                <td><?= $row["TenHP"] ?></td>
                                <td><span class="credit-badge"><?= $row["SoTinChi"] ?> tín chỉ</span></td>
                                <td><span class="quantity-badge"><?= $row["SoLuong"] ?></span></td>
                                <td><button type="submit" name="MaHP" value="<?= $row["MaHP"] ?>" class="btn">➕ Đăng Ký</button></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="links">
            <a href="index.php" class="btn-backhome">🔙 Quay lại</a>
            <a href="logout.php" class="btn-logout">🚪 Đăng xuất</a>
            <a href="giohang.php" class="btn-cart">🛒 Xem Giỏ Hàng</a>
        </div>
    </div>
</body>

</html>