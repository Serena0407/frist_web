<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊｜輔仁大學貴重儀器預約系統</title>
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

        /* Breadcrumb Styles */
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
            padding: 60px 60px;
            margin: 130px;
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
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="login.html">線上預約規則</a></li>
            <li class="breadcrumb-item active" aria-current="page">註冊</li>
        </ol>
    </nav>

    <!-- User Registration Form Start -->
    <div class="wrapper">
        <form id="SignupForm" onsubmit="return validateForm()" method="POST">
            <center><h1>使用者註冊</h1></center><br>
            <div class="input-box">
                <input type="text" id="accountId" name="account" placeholder="學號 Student ID（9碼數字）" required pattern="\d{9}">
                <i class='bx bx-user'></i>
            </div>
            <div class="input-box">
                <input type="text" id="name" name="name" placeholder="姓名 Name" required>
                <i class='bx bx-user'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="pw" placeholder="密碼 Password" required>
                <i class='bx bx-lock-open-alt'></i>
            </div>
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="電子郵件 Email" required>
                <i class='bx bx-envelope'></i>
            </div>
            <button type="submit" class="btn">註冊</button>
            <div id="errorMessage1" class="error-message1"></div>
        </form>
    </div>
    <!-- User Registration Form End -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? null;
        $account = $_POST['account'] ?? null;
        $pw = $_POST['pw'] ?? null;
        $email = $_POST['email'] ?? null;

        if (!empty($name) && !empty($account) && !empty($pw) && !empty($email)) {
            $link = mysqli_connect('localhost', 'root', '', 'reservation');

            if (!$link) {
                die("資料庫連接失敗：" . mysqli_connect_error());
            }

            $name = mysqli_real_escape_string($link, $name);
            $account = mysqli_real_escape_string($link, $account);
            $pw = mysqli_real_escape_string($link, $pw);
            $email = mysqli_real_escape_string($link, $email);

            $sql = "INSERT INTO user (name, account, pw, email) VALUES ('$name', '$account', '$pw', '$email')";

            if (mysqli_query($link, $sql)) {
                echo "<script>alert('註冊完成'); window.location.href = 'logpage.html';</script>";
            } else {
                echo "新增失敗：" . mysqli_error($link);
            }

            mysqli_close($link);
        } else {
            echo "請填寫完整表單！";
        }
    }
    ?>
</body>
</html>
