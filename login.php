<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = trim($_POST["MaSV"]);
    $MatKhau = $_POST["MatKhau"];

    $sql = "SELECT MaSV, MatKhau FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!preg_match('/^\d{10}$/', $MaSV)) {
        $errors[] = "Mã sinh viên phải có đúng 10 chữ số.";
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!empty($row['MatKhau']) && password_verify($MatKhau, $row['MatKhau'])) {
            $_SESSION["MaSV"] = $MaSV;
            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Mã số sinh viên hoặc mật khẩu không đúng!";
        }
    } else {
        $error = "❌ Mã số sinh viên hoặc mật khẩu không đúng!";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f9fafb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            width: 320px;
            max-width: 90%;
        }

        h2 {
            color: #2563eb;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-top: 12px;
            margin-bottom: 6px;
            text-align: left;
            font-size: 0.9rem;
            color: #4b5563;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px;
            margin-top: 10px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95rem;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-login:hover {
            background: #1d4ed8;
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .error-message {
            color: #dc2626;
            font-size: 0.9rem;
            margin-top: 15px;
            padding: 10px;
            background-color: #fee2e2;
            border-radius: 6px;
            text-align: center;
        }

        p {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>🔑 Đăng Nhập</h2>
        <form method="post">
            <label for="MaSV">Mã số sinh viên:</label>
            <input type="text" name="MaSV" id="MaSV" placeholder="Nhập mã số sinh viên" required>

            <label for="MatKhau">Mật khẩu:</label>
            <input type="password" name="MatKhau" id="MatKhau" placeholder="Nhập mật khẩu" required>

            <button type="submit" class="btn-login">🚀 Đăng nhập</button>
        </form>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>
        <p>Bạn chưa có tài khoản? Hãy <a href='register.php'>đăng ký</a>.</p>
    </div>
</body>

</html>