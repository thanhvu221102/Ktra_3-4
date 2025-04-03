<?php
include 'db.php';
include 'header.php';
$id = $_GET['id'];
$result = $conn->query("SELECT s.*, n.TenNganh FROM SinhVien s 
                        LEFT JOIN nganhhoc n ON s.MaNganh = n.MaNganh 
                        WHERE s.MaSV='$id'");
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sinh Viên</title>
    <style>
        body {
            font-family: 'DM Sans', 'Helvetica Neue', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .detail-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            display: grid;
            grid-template-columns: 300px 1fr;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border-radius: 2px;
            overflow: hidden;
        }

        .image-section {
            background: #ff6b6b;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .image-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .student-img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .student-id {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 1px;
            z-index: 1;
        }

        .info-section {
            padding: 40px;
        }

        h2 {
            color: #333;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        h2::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 40px;
            height: 4px;
            background: #ff6b6b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-label {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 5px;
            display: block;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #333;
        }

        .btn-back {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: #ff6b6b;
            background: transparent;
            padding: 10px 20px;
            border: 2px solid #ff6b6b;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #ff6b6b;
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .detail-container {
                grid-template-columns: 1fr;
                margin: 20px;
            }
            
            .image-section {
                padding: 30px 20px;
            }
            
            .info-section {
                padding: 30px 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="detail-container">
        <div class="image-section">
            <img src="<?= $data['Hinh'] ?>" alt="Ảnh Sinh Viên" class="student-img">
            <div class="student-id">MSSV: <?= $data['MaSV'] ?></div>
        </div>
        
        <div class="info-section">
            <h2>Thông Tin Sinh Viên</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Họ và Tên</span>
                    <div class="info-value"><?= $data['HoTen'] ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Giới Tính</span>
                    <div class="info-value"><?= $data['GioiTinh'] ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Ngày Sinh</span>
                    <div class="info-value"><?= date('d/m/Y', strtotime($data['NgaySinh'])) ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Mã Ngành</span>
                    <div class="info-value"><?= $data['MaNganh'] ?></div>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Tên Ngành</span>
                    <div class="info-value"><?= $data['TenNganh'] ?? 'Chưa có thông tin' ?></div>
                </div>
            </div>
            
            <a href="index.php" class="btn-back">🔙 Quay lại danh sách</a>
        </div>
    </div>
</body>
</html>