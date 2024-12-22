<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in | 輔仁大學教室預借系統</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            font-size: 17px;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .breadcrumb-container {
            width: 100%;
            background: rgba(255, 255, 255, 0.8);
            padding: 25px 20px;
            margin: 0 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.114);
        }

        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item {
            font-size: 17px;
            color: #003D79;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            padding: 0 10px;
            color: #003D79;
        }

        .breadcrumb-item a {
            color: #003D79;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        /* Login Form Styles */
        .wrapper {
            width: 500px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, .2);
            color: #003D79;
            border-radius: 20px;
            padding: 40px 60px;
            margin: 200px;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            height: 50px;
            background: transparent;
            border: 2px solid rgba(193, 221, 255, 0.772);
            border-radius: 40px;
            font-size: 16px;
            color: #84C1FF;
            padding: 15px 45px 15px 20px;
        }

        .input-box input::placeholder {
            color: #858585;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #84C1FF;
        }

        .btn {
            width: 100%;
            height: 45px;
            background: #8cc6fd;
            border: none;
            border-radius: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }

        .register-link {
            font-size: 14.5px;
            text-align: center;
            margin-top: 20px;
        }

        .register-link p a {
            color: #84C1FF;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link p a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }

        h2 {
            font-size: 20px;
        }

    </style>
</head>
<body>
<?php
 // 啟用 session
    $error_message = ""; // 用來儲存錯誤訊息
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $account = $_POST['account'];
        $_SESSION['account'] = $account;
        $pw = $_POST['pw'];
        $_SESSION['pw'] = $pw;
        $link = mysqli_connect('localhost', 'root', '', 'reservation');
        $sql = "SELECT * FROM manager WHERE account='$account' AND pw='$pw'";
        $result = mysqli_query($link, $sql);

        // 檢查帳號和密碼是否正確
        if ($row = mysqli_fetch_assoc($result)) {
            // 帳號和密碼正確
            $name = $row['name'];
            $email = $row['email'];

            // 跳轉到 manager.php
            header("Location: manager.php?account=" . urlencode($account));
            exit;
        } else {
            // 如果帳號或密碼錯誤，顯示錯誤訊息
            $error_message = "帳號或密碼錯誤";
        }
    }
?>
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="login.html">管理者登入</a></li>
        </ol>
    </nav>
    <div class="wrapper">
        <form id="loginForm" action="logpage3.php" method="post">
            <center><h2>請輸入帳號登入(9碼數字) <br>Login (Manager ID)</h2></center><br>
            <div class="input-box">
                <input type="text" name="account" id="accountId" placeholder="帳號 Manager ID" required pattern="\d{9}">
                <i class='bx bx-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="pw" id="password" placeholder="密碼 Password" required>
                <i class='bx bx-lock-open-alt'></i>
            </div>
            <button type="submit" class="btn">登入</button>

            <?php
            // 如果有錯誤訊息，顯示在表單下方
            if ($error_message) {
                echo "<div class='error-message'>$error_message</div>";
            }
            ?>
        </form>
    </div>
</body>
</html>
