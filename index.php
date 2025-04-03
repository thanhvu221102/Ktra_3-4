<?php
include 'db.php';
include 'header.php';

// Lấy danh sách ngành học để hiển thị trong bộ lọc
$nganhHoc = $conn->query("SELECT * FROM nganhhoc");

// Xử lý tìm kiếm nâng cao
$whereClauses = [];
$params = [];
$types = "";

if (!empty($_GET['MaSV'])) {
    $whereClauses[] = "MaSV LIKE ?";
    $params[] = "%" . $_GET['MaSV'] . "%";
    $types .= "s";
}

if (!empty($_GET['HoTen'])) {
    $whereClauses[] = "HoTen LIKE ?";
    $params[] = "%" . $_GET['HoTen'] . "%";
    $types .= "s";
}

if (!empty($_GET['GioiTinh'])) {
    $whereClauses[] = "GioiTinh = ?";
    $params[] = $_GET['GioiTinh'];
    $types .= "s";
}

if (!empty($_GET['MaNganh'])) {
    $whereClauses[] = "MaNganh = ?";
    $params[] = $_GET['MaNganh'];
    $types .= "s";
}

// Tạo câu lệnh SQL động
$sql = "SELECT * FROM SinhVien";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sinh Viên</title>
    <style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f8f9fa;
    text-align: center;
    margin: 0;
    padding: 0;
    color: #333;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

h2 {
    color: #0056b3;
    margin-bottom: 25px;
    font-weight: 600;
    position: relative;
    padding-bottom: 10px;
}

h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: #0056b3;
    border-radius: 3px;
}

form {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

input,
select {
    padding: 10px 15px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s;
}

input:focus,
select:focus {
    outline: none;
    border-color: #0056b3;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

button {
    padding: 10px 20px;
    background: #0056b3;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}

button:hover {
    background: #004494;
    transform: translateY(-2px);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 25px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

th, td {
    padding: 15px 10px;
    text-align: center;
    border: none;
}

th {
    background: #0056b3;
    color: white;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 0.5px;
}

tr:nth-child(even) {
    background-color: #f8f9fa;
}

tr:hover {
    background-color: #e9ecef;
}

.student-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.student-img:hover {
    transform: scale(1.1);
}

.btn {
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 6px;
    color: white;
    margin: 3px;
    font-size: 13px;
    font-weight: 500;
    display: inline-block;
    transition: all 0.3s;
    border: none;
}

.btn:hover {
    opacity: 1;
    transform: translateY(-2px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

.btn-view {
    background: #28a745;
}

.btn-view:hover {
    background: #218838;
}

.btn-edit {
    background: #ffc107;
    color: #212529;
}

.btn-edit:hover {
    background: #e0a800;
}

.btn-delete {
    background: #dc3545;
}

.btn-delete:hover {
    background: #c82333;
}

.btn-add {
    display: inline-block;
    margin-top: 25px;
    background: #0056b3;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-add:hover {
    background: #004494;
    transform: translateY(-3px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .container {
        width: 95%;
        padding: 15px;
    }
    
    table {
        font-size: 14px;
    }
    
    .btn {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .student-img {
        width: 45px;
        height: 45px;
    }
}
    </style>
</head>

<body>

    <div class="container">
        <h2>Danh Sách Sinh Viên</h2>

        <!-- Biểu mẫu tìm kiếm -->
        

        <table>
            <tr>
                <th>Mã SV</th>
                <th>Họ Tên</th>
                <th>Giới Tính</th>
                <th>Ngày Sinh</th>
                <th>Hình</th>
                <th>Mã Ngành</th>
                <th>Hành Động</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['MaSV'] ?></td>
                    <td><?= $row['HoTen'] ?></td>
                    <td><?= $row['GioiTinh'] ?></td>
                    <td><?= $row['NgaySinh'] ?></td>
                    <td><img src="<?= $row['Hinh'] ?>" class="student-img"></td>
                    <td><?= $row['MaNganh'] ?></td>
                    <td>
                        <a href="detail.php?id=<?= $row['MaSV'] ?>" class="btn btn-view">Chi tiết</a>
                        <a href="edit.php?id=<?= $row['MaSV'] ?>" class="btn btn-edit">Sửa</a>
                        <a href="delete.php?id=<?= $row['MaSV'] ?>" class="btn btn-delete" onclick="return confirm('Xóa sinh viên này?')">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="add.php" class="btn btn-add">➕ Thêm Sinh Viên</a>
    </div>

</body>

</html>