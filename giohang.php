<?php
include 'db.php';
session_start();

if (!isset($_SESSION["MaSV"])) {
    header("Location: login.php");
    exit();
}

$MaSV = $_SESSION["MaSV"];

// Xóa 1 học phần khỏi giỏ hàng
if (isset($_GET["remove"])) {
    $MaHP = $_GET["remove"];
    $_SESSION["cart"] = array_diff($_SESSION["cart"], [$MaHP]);
}

// Xóa tất cả học phần khỏi giỏ hàng
if (isset($_GET["clear"])) {
    $_SESSION["cart"] = [];
}

// Kiểm tra giỏ hàng có dữ liệu không
$hocPhanList = [];
$totalCredits = 0;

if (!empty($_SESSION["cart"])) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f9f4ff;
            background-image: linear-gradient(120deg, #f9f4ff 0%, #f3e7ff 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
        }

        .cart-container {
            max-width: 900px;
            margin: 40px auto;
            background: transparent;
        }

        .cart-header {
            text-align: left;
            margin-bottom: 30px;
            position: relative;
        }

        h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #6c5ce7;
            margin: 0 0 10px 0;
            display: inline-block;
        }

        .cart-summary {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-text {
            font-size: 1rem;
            color: #666;
        }

        .summary-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #6c5ce7;
            margin-left: 10px;
        }

        .cart-items {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .cart-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .cart-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.15);
        }

        .item-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            flex: 1;
        }

        .item-code {
            font-size: 0.85rem;
            color: #6c5ce7;
            font-weight: 600;
            background: #f3e7ff;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-credits {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: #666;
        }

        .credit-icon {
            margin-right: 5px;
            color: #6c5ce7;
        }

        .item-actions {
            display: flex;
            align-items: center;
        }

        .btn-remove {
            background: transparent;
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-remove:hover {
            background: #fff8f8;
            transform: translateY(-2px);
        }

        .empty-cart {
            background: white;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.1);
        }

        .empty-cart p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
        }

        .btn-back {
            background: transparent;
            color: #6c5ce7;
            border: 1px solid #6c5ce7;
        }

        .btn-back:hover {
            background: #f3e7ff;
            transform: translateY(-2px);
        }

        .btn-clear {
            background: transparent;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }

        .btn-clear:hover {
            background: #fff8f8;
            transform: translateY(-2px);
        }

        .btn-confirm {
            background: #6c5ce7;
            color: white;
            border: none;
        }

        .btn-confirm:hover {
            background: #5b4ecc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
        }

        @media (max-width: 768px) {
            .cart-container {
                margin: 20px auto;
            }
            
            .cart-summary {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .summary-item {
                margin-bottom: 10px;
            }
            
            .cart-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .item-actions {
                margin-top: 15px;
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h2>🛒 Giỏ Hàng Học Phần</h2>
        </div>

        <?php if (empty($hocPhanList)): ?>
            <div class="empty-cart">
                <p>📌 Giỏ hàng của bạn đang trống</p>
                <a href="hocphan.php" class="btn btn-back">🔙 Quay lại chọn học phần</a>
            </div>
        <?php else: ?>
            <div class="cart-summary">
                <div class="summary-item">
                    <span class="summary-text">Tổng số học phần:</span>
                    <span class="summary-value"><?= count($hocPhanList) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-text">Tổng số tín chỉ:</span>
                    <span class="summary-value"><?= $totalCredits ?></span>
                </div>
            </div>

            <div class="cart-items">
                <?php foreach ($hocPhanList as $hp): ?>
                    <div class="cart-item">
                        <div class="item-info">
                            <span class="item-code"><?= $hp["MaHP"] ?></span>
                            <div class="item-name"><?= $hp["TenHP"] ?></div>
                            <div class="item-credits">
                                <span class="credit-icon">📚</span>
                                <?= $hp["SoTinChi"] ?> tín chỉ
                            </div>
                        </div>
                        <div class="item-actions">
                            <a href="giohang.php?remove=<?= $hp["MaHP"] ?>" class="btn-remove">❌ Xóa</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-actions">
                <a href="hocphan.php" class="btn btn-back">🔙 Quay lại</a>
                <a href="giohang.php?clear=1" class="btn btn-clear">🗑 Xóa Tất Cả</a>
                <a href="xacnhan.php" class="btn btn-confirm">✅ Xác Nhận Đăng Ký</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>