<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Lý Đăng Ký Học Phần</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, #0062cc, #0056b3);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        h1 {
            margin: 0;
            padding: 10px 0;
            font-size: 1.8rem;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
            text-align: center;
        }

        nav {
            margin-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 10px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav ul li {
            margin: 5px 15px;
            position: relative;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            text-decoration: none;
        }

        nav ul li a:active {
            transform: translateY(0);
        }

        .active-link {
            background-color: rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }

        .user-status {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            header {
                padding: 10px 0;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            nav ul li {
                margin: 5px 0;
                width: 100%;
                text-align: center;
            }
            
            nav ul li a {
                display: block;
                padding: 10px;
            }
        }

        /* Animation for menu items */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        nav ul li {
            animation: fadeIn 0.3s ease forwards;
            animation-delay: calc(0.1s * var(--i, 0));
            opacity: 0;
        }

        nav ul li:nth-child(1) { --i: 1; }
        nav ul li:nth-child(2) { --i: 2; }
        nav ul li:nth-child(3) { --i: 3; }
        nav ul li:nth-child(4) { --i: 4; }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <h1>Hệ Thống Quản Lý Đăng Ký Học Phần</h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active-link' : '' ?>">Sinh Viên</a></li>
                    <li><a href="hocphan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'hocphan.php' ? 'active-link' : '' ?>">Học Phần</a></li>
                    <li><a href="giohang.php" class="<?= basename($_SERVER['PHP_SELF']) == 'giohang.php' ? 'active-link' : '' ?>">Giỏ Hàng</a></li>

                    <?php if (isset($_SESSION["MaSV"])): ?>
                        <li><a href="logout.php" class="user-status">Đăng Xuất (<?= $_SESSION["MaSV"] ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="<?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active-link' : '' ?>">Đăng Nhập</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
</body>

</html>