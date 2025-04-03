<?php
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM SinhVien WHERE MaSV='$id'");
$data = $result->fetch_assoc();

// Lấy danh sách ngành học từ bảng nganhhoc
$nganh_result = $conn->query("SELECT MaNganh, TenNganh FROM nganhhoc");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $MaNganh = $_POST['MaNganh'];

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
    $stmt = $conn->prepare("SELECT COUNT(*) FROM NganhHoc WHERE MaNganh = ?");
    $stmt->bind_param("s", $MaNganh);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        $errors[] = "Mã ngành không tồn tại.";
    }
    // Xử lý upload ảnh mới nếu có
    if (!empty($_FILES["Hinh"]["name"])) {
        $target_dir = "uploads/";  
        $target_file = $target_dir . basename($_FILES["Hinh"]["name"]);
        move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file);
    } else {
        $target_file = $data['Hinh'];
    }
    if (empty($errors)) {
        $conn->query("UPDATE SinhVien SET HoTen='$HoTen', GioiTinh='$GioiTinh', NgaySinh='$NgaySinh', MaNganh='$MaNganh', Hinh='$target_file' WHERE MaSV='$id'");
        header("Location: index.php");
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sinh Viên</title>
    <style>
        body {
            font-family: 'Roboto', 'Arial', sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 20px;
            color: #334155;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 0;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr;
        }

        h2 {
            background: #6366f1;
            color: white;
            margin: 0;
            padding: 20px 30px;
            font-weight: 500;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            border-left: 8px solid #4f46e5;
        }

        .form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            padding: 30px;
            align-items: start;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .image-preview-container {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f8fafc;
            padding: 20px;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
        }

        label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 0.95rem;
            background: #f8fafc;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .gender-group {
            display: flex;
            gap: 20px;
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
            width: 20px;
            height: 20px;
            border: 2px solid #6366f1;
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
            background: #6366f1;
            border-radius: 50%;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: white;
        }

        input[type="file"]::file-selector-button {
            background: #6366f1;
            color: white;
            border: none;
            padding: 8px 16px;
            margin-right: 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #4f46e5;
        }

        .current-image {
            margin: 15px 0;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
        }

        #preview {
            margin: 15px 0;
            border-radius: 4px;
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border: 2px solid #6366f1;
        }

        .buttons-container {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-update {
            background: #6366f1;
            color: white;
        }

        .btn-update:hover {
            background: #4f46e5;
            transform: translateY(-2px);
        }

        .btn-back {
            background: transparent;
            color: #6366f1;
            border: 1px solid #6366f1;
        }

        .btn-back:hover {
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .error {
            grid-column: 1 / -1;
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 12px 15px;
            color: #b91c1c;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .error p {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .container {
                width: 95%;
            }
            
            .buttons-container {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✏️ Sửa Thông Tin Sinh Viên</h2>
    
    <form method="POST" enctype="multipart/form-data" class="form-container">
        <?php if (!empty($errors)) { ?>
            <div class="error">
                <?php foreach ($errors as $error) {
                    echo "<p>❌ $error</p>";
                } ?>
            </div>
        <?php } ?>
        
        <div class="form-group">
            <label for="HoTen">Họ Tên:</label>
            <input type="text" id="HoTen" name="HoTen" value="<?= $data['HoTen'] ?>" required>
        </div>
        
        <div class="form-group">
            <label for="NgaySinh">Ngày Sinh:</label>
            <input type="date" name="NgaySinh" id="NgaySinh" value="<?= $data['NgaySinh'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Giới Tính:</label>
            <div class="gender-group">
                <label class="radio-container">
                    <input type="radio" name="GioiTinh" value="Nam" <?= ($data['GioiTinh'] == "Nam") ? "checked" : "" ?>>
                    Nam
                </label>
                <label class="radio-container">
                    <input type="radio" name="GioiTinh" value="Nữ" <?= ($data['GioiTinh'] == "Nữ") ? "checked" : "" ?>>
                    Nữ
                </label>
                <label class="radio-container">
                    <input type="radio" name="GioiTinh" value="Khác" <?= ($data['GioiTinh'] == "Khác") ? "checked" : "" ?>>
                    Khác
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="MaNganh">Ngành Học:</label>
            <select name="MaNganh" id="MaNganh">
                <?php while ($row = $nganh_result->fetch_assoc()): ?>
                    <option value="<?= $row['MaNganh'] ?>" <?= ($row['MaNganh'] == $data['MaNganh']) ? "selected" : "" ?>>
                        <?= $row['MaNganh'] ?> - <?= $row['TenNganh'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group full-width image-preview-container">
            <label>Hình Ảnh:</label>
            
            <?php if (!empty($data['Hinh'])): ?>
                <img src="<?= $data['Hinh'] ?>" class="current-image" alt="Hình hiện tại">
                <p>Hình ảnh hiện tại</p>
            <?php endif; ?>
            
            <input type="file" name="Hinh" id="fileInput" accept="image/*">
            <img id="preview" src="/placeholder.svg" alt="Xem trước ảnh" style="display: none;">
        </div>
        
        <div class="buttons-container">
            <a href="index.php" class="btn btn-back">🔙 Quay lại</a>
            <button type="submit" class="btn btn-update">💾 Cập Nhật</button>
        </div>
    </form>
</div>

<script>
    document.getElementById("fileInput").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("preview").src = e.target.result;
                document.getElementById("preview").style.display = "block";
            }
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>