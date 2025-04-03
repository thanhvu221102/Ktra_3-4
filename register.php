<?php
session_start();
include 'db.php';

$error = "";
$success = "";

function validatePassword($password)
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{6,}$/', $password);
}

$MaSV = $HoTen = $MaNganh = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = trim($_POST["MaSV"]);
    $HoTen = trim($_POST["HoTen"]);
    $MaNganh = $_POST["MaNganh"];
    $MatKhau = $_POST["MatKhau"];
    $ConfirmMatKhau = $_POST["ConfirmMatKhau"];

    if (!preg_match('/^\d{10}$/', $MaSV)) {
        $error = "❌ Mã số sinh viên phải có đúng 10 chữ số!";
    } elseif (!preg_match('/^\p{Lu}\p{Ll}+(?: \p{Lu}\p{Ll}+)*$/u', $HoTen)) {
        $error = "❌ Họ tên không hợp lệ! Mỗi họ, tên, tên lót phải viết hoa chữ cái đầu.";
    } elseif (empty($MaNganh)) {
        $error = "❌ Vui lòng chọn ngành học!";
    } elseif ($MatKhau !== $ConfirmMatKhau) {
        $error = "❌ Mật khẩu xác nhận không khớp!";
    } elseif (!validatePassword($MatKhau)) {
        $error = "❌ Mật khẩu phải có ít nhất một chữ hoa, một chữ thường, một ký tự đặc biệt và không chứa khoảng trắng!";
    } else {
        $hashedPassword = password_hash($MatKhau, PASSWORD_DEFAULT);
        $sql = "INSERT INTO SinhVien (MaSV, HoTen, MaNganh, MatKhau) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $MaSV, $HoTen, $MaNganh, $hashedPassword);

        if ($stmt->execute()) {
            $success = "✅ Đăng ký thành công!";
            $MaSV = $HoTen = $MaNganh = "";
        } else {
            $error = "❌ Mã số sinh viên đã tồn tại!";
        }

        $stmt->close();
    }
}

// Lấy danh sách ngành học
$nganhList = $conn->query("SELECT MaNganh, TenNganh FROM nganhhoc");
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .register-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            width: 380px;
            max-width: 100%;
            padding: 35px;
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #22c55e, #10b981);
        }

        h2 {
            font-weight: 700;
            color: #111827;
            margin-top: 0;
            margin-bottom: 30px;
            font-size: 1.5rem;
            text-align: left;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 6px;
            text-align: left;
        }

        input, select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #f9fafb;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #10b981;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        .btn-register {
            background: #10b981;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-register:active {
            transform: translateY(1px);
        }

        .error-message, .success-message {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin: 20px 0;
            text-align: left;
        }

        .error-message {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }

        .success-message {
            background-color: #dcfce7;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        p {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 25px;
            text-align: center;
        }

        a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        a:hover {
            color: #059669;
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .register-container {
                padding: 25px;
                border-radius: 12px;
            }
            
            h2 {
                font-size: 1.3rem;
            }
            
            input, select, .btn-register {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="register-container">
        <h2>📝 Đăng Ký Tài Khoản</h2>
        <form method="post">
            <label for="MaSV">Mã số sinh viên:</label>
            <input type="text" name="MaSV" id="MaSV" placeholder="Nhập mã số sinh viên" required value="<?= htmlspecialchars($MaSV) ?>">

            <label for="HoTen">Họ và tên:</label>
            <input type="text" name="HoTen" id="HoTen" placeholder="Nhập họ và tên" required value="<?= htmlspecialchars($HoTen) ?>">

            <label for="MaNganh">Ngành học:</label>
            <select name="MaNganh" id="MaNganh" required>
                <option value="">-- Chọn ngành học --</option>
                <?php while ($row = $nganhList->fetch_assoc()): ?>
                    <option value="<?= $row['MaNganh'] ?>" <?= ($MaNganh == $row['MaNganh']) ? 'selected' : '' ?>><?= $row['TenNganh'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="MatKhau">Mật khẩu:</label>
            <input type="password" name="MatKhau" id="MatKhau" placeholder="Nhập mật khẩu" required>

            <label for="ConfirmMatKhau">Xác nhận mật khẩu:</label>
            <input type="password" name="ConfirmMatKhau" id="ConfirmMatKhau" placeholder="Nhập lại mật khẩu" required>

            <button type="submit" class="btn-register">🚀 Đăng ký</button>
        </form>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <p class="success-message"><?= $success ?></p>
        <?php endif; ?>
        <p>Bạn đã có tài khoản? Hãy <a href='login.php'>đăng nhập</a>.</p>
    </div>

</body>

</html>