<?php
include 'db.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = trim($_POST['MaSV']);
    $HoTen = trim($_POST['HoTen']);
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $MaNganh = $_POST['MaNganh'];
    $Hinh = "";

    // Kiểm tra mã số sinh viên (10 chữ số)
    if (!preg_match('/^\d{10}$/', $MaSV)) {
        $errors[] = "Mã sinh viên phải có đúng 10 chữ số.";
    }

    // Kiểm tra họ tên (chỉ chứa chữ cái và khoảng trắng)
    if (!preg_match('/^[a-zA-ZÀ-ỹ\s]+$/u', $HoTen)) {
        $errors[] = "Họ tên chỉ được chứa chữ cái và khoảng trắng.";
    }

    // Kiểm tra giới tính hợp lệ
    if (!in_array($GioiTinh, ['Nam', 'Nữ', 'Khác'])) {
        $errors[] = "Giới tính không hợp lệ.";
    }

    // Kiểm tra ngày sinh hợp lệ (không được lớn hơn ngày hiện tại)
    if (!empty($NgaySinh) && strtotime($NgaySinh) > time()) {
        $errors[] = "Ngày sinh không được lớn hơn ngày hiện tại.";
    }

    // Kiểm tra mã ngành có tồn tại không
    $stmt = $conn->prepare("SELECT COUNT(*) FROM NganhHoc WHERE MaNganh = ?");
    $stmt->bind_param("s", $MaNganh);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        $errors[] = "Mã ngành không tồn tại.";
    }

    // Xử lý upload hình ảnh nếu có
    if (!empty($_FILES["Hinh"]["name"])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES["Hinh"]["type"];
        $file_size = $_FILES["Hinh"]["size"];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Chỉ chấp nhận hình ảnh JPG, PNG hoặc GIF.";
        }
        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = "Kích thước hình ảnh tối đa là 2MB.";
        }

        if (empty($errors)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["Hinh"]["name"]);
            move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file);
            $Hinh = $target_file;
        }
    }

    // Nếu không có lỗi, thêm sinh viên vào CSDL
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $MaSV, $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit();
    }
}

// Lấy danh sách mã ngành
$sql = "SELECT MaNganh, TenNganh FROM NganhHoc";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh Viên</title>
    <style>
        body {
            font-family: 'Poppins', system-ui, sans-serif;
            background: #f7f3f0;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #2d2d2d;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #fff;
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            border-radius: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr;
        }

        h2 {
            background: #9c6644;
            color: white;
            margin: 0;
            padding: 20px;
            font-weight: 600;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
            text-align: left;
        }

        form {
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            align-items: start;
        }

        label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: none;
            background: #f7f3f0;
            border-radius: 0;
            font-size: 0.9rem;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            background: #f0e9e4;
            box-shadow: inset 0 -2px 0 #9c6644;
        }

        .gender-group {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }

        .radio-container {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #9c6644;
            border-radius: 50%;
            margin-right: 8px;
            position: relative;
            cursor: pointer;
        }

        input[type="radio"]:checked::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: #9c6644;
            border-radius: 50%;
        }

        input[type="file"] {
            padding: 10px;
            background: #f7f3f0;
            border: 1px dashed #ccc;
        }

        input[type="file"]::file-selector-button {
            background: #9c6644;
            color: white;
            border: none;
            padding: 8px 16px;
            margin-right: 15px;
            border-radius: 0;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #7d5236;
        }

        button {
            background: #9c6644;
            color: white;
            border: none;
            padding: 14px;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            letter-spacing: 0.5px;
            border-radius: 0;
        }

        button:hover {
            background: #7d5236;
        }

        .btn-back {
            display: inline-block;
            text-decoration: none;
            color: #9c6644;
            background: transparent;
            padding: 12px 20px;
            border: 1px solid #9c6644;
            text-align: center;
            margin-top: 0;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-back:hover {
            background: #f0e9e4;
        }

        .error {
            background: #fff5f5;
            border-left: 4px solid #e53e3e;
            padding: 12px 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .error p {
            margin: 5px 0;
            color: #e53e3e;
            font-size: 0.9rem;
        }

        .form-footer {
            padding: 0 30px 30px;
            text-align: center;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239c6644' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }

        @media (max-width: 600px) {
            .container {
                width: 95%;
                margin: 15px auto;
            }
            
            form {
                padding: 20px;
            }
            
            .form-footer {
                padding: 0 20px 20px;
            }
            
            .gender-group {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📌 Thêm Sinh Viên</h2>

        <?php if (!empty($errors)) { ?>
            <div class="error">
                <?php foreach ($errors as $error) {
                    echo "<p>❌ $error</p>";
                } ?>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">
            <div>
                <label for="MaSV">Mã SV:</label>
                <input type="text" name="MaSV" id="MaSV" required value="<?= isset($MaSV) ? $MaSV : '' ?>">
            </div>

            <div>
                <label for="HoTen">Họ Tên:</label>
                <input type="text" name="HoTen" id="HoTen" required value="<?= isset($HoTen) ? $HoTen : '' ?>">
            </div>

            <div>
                <label>Giới Tính:</label>
                <div class="gender-group">
                    <label class="radio-container">
                        <input type="radio" name="GioiTinh" value="Nam" <?= (isset($GioiTinh) && $GioiTinh == "Nam") ? "checked" : "" ?>>
                        Nam
                    </label>
                    <label class="radio-container">
                        <input type="radio" name="GioiTinh" value="Nữ" <?= (isset($GioiTinh) && $GioiTinh == "Nữ") ? "checked" : "" ?>>
                        Nữ
                    </label>
                    <label class="radio-container">
                        <input type="radio" name="GioiTinh" value="Khác" <?= (isset($GioiTinh) && $GioiTinh == "Khác") ? "checked" : "" ?>>
                        Khác
                    </label>
                </div>
            </div>

            <div>
                <label for="NgaySinh">Ngày Sinh:</label>
                <input type="date" name="NgaySinh" id="NgaySinh" value="<?= isset($NgaySinh) ? $NgaySinh : '' ?>">
            </div>

            <div>
                <label for="MaNganh">Ngành học:</label>
                <select name="MaNganh" id="MaNganh" required>
                    <option value="">-- Chọn Mã Ngành --</option>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?= $row['MaNganh'] ?>"><?= $row['MaNganh'] ?> - <?= $row['TenNganh'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label for="Hinh">Hình Ảnh:</label>
                <input type="file" name="Hinh" id="Hinh" accept="image/*">
            </div>
            
            <button id="add-button" type="submit">✅ Lưu</button>
        </form>
        
        <div class="form-footer">
            <a href="index.php" class="btn-back">🔙 Quay lại danh sách</a>
        </div>
    </div>
</body>
</html>