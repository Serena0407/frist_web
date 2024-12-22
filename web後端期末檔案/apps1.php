<?php
// 連接資料庫
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation"; // 記得更換為你的資料庫名稱

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationType = $_POST['reservation_type'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $daysOfWeek = implode(",", $_POST['day_of_week']);
    $timeSlots = implode(",", $_POST['time_slot']);

    // 檢查日期和時間的有效性
    $currentDate = date('Y-m-d');
    if ($reservationType == 'short_term') {
        $maxDate = date('Y-m-d', strtotime('+29 days', strtotime($currentDate))); // 29天內
        if ($startDate < $currentDate || $startDate > $maxDate || $endDate < $currentDate || $endDate > $maxDate) {
            $message = "短期預約只能在今天到未來29天內的日期範圍內進行！";
        }
    }

    // 確保選擇的日期不過去
    if ($startDate < $currentDate || $endDate < $currentDate) {
        $message = "選擇的日期不可為過去時間！";
    }

    // 檢查日期與時間是否有衝突
    $sqlCheck = "SELECT * FROM reservations WHERE ('$startDate' <= end_date AND '$endDate' >= start_date)
                 AND FIND_IN_SET(day_of_week, '$daysOfWeek') > 0
                 AND FIND_IN_SET(time_slot, '$timeSlots') > 0";
    $result = $conn->query($sqlCheck);

    if ($result->num_rows > 0) {
        $message = "時間段已被預約，請選擇其他時間！";
    } else {
        // 插入新的預約資料
        $sqlInsert = "INSERT INTO reservations (reservation_type, start_date, end_date, day_of_week, time_slot)
                      VALUES ('$reservationType', '$startDate', '$endDate', '$daysOfWeek', '$timeSlots')";
        if ($conn->query($sqlInsert) === TRUE) {
            $message = "預約成功！";
        } else {
            $message = "錯誤: " . $conn->error;
        }
    }
}

// 顯示已預約的時間段
$sqlReservations = "SELECT * FROM reservations";
$reservations = $conn->query($sqlReservations);
$reservedSlots = [];
while ($row = $reservations->fetch_assoc()) {
    $reservedSlots[] = [
        'start_date' => $row['start_date'],
        'end_date' => $row['end_date'],
        'day_of_week' => $row['day_of_week'],
        'time_slot' => $row['time_slot']
    ];
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <title>線上預約｜輔仁大學教室預借系統</title>
    <link rel="icon" href="http://home.lib.fju.edu.tw/TC/sites/default/files/shield-FJCULIB-FJCU.png" type="image/png">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
        }
        h1 {
            text-align: center;
            margin-top: 50px;
            font-size: 36px;
            font-weight: 600;
            color: #343a40;
        }
        form {
            width: 70%;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }
        .form-label {
            font-weight: 500;
            font-size: 16px;
            color: #495057;
        }
        select, input[type="date"], input[type="button"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ced4da;
            background-color: #f1f3f5;
            font-size: 16px;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .checkbox-group input {
            margin-bottom: 10px;
        }
        .reservation-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }
        .reservation-btn:hover {
            background-color: #0056b3;
        }
        .reserved-slots {
            margin-top: 50px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .reserved-slots ul {
            list-style-type: none;
            padding: 0;
        }
        .reserved-slots li {
            margin-bottom: 15px;
            font-size: 16px;
            color: #495057;
        }
        .reserved-slots li span {
            font-weight: bold;
        }
        .footer {
            background-color: #343a40;
            color: #ffffff;
            padding: 40px 0;
            text-align: center;
        }
        .footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
     <!-- Navbar & Hero Start -->
     <div class="container-fluid position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 px-lg-5 py-3 py-lg-0">
                <a href="about.html" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"><b>輔仁大學教室預借系統</b></h1>
                    <!-- <img src="img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="classroomDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                教室預約系統
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="classroomDropdown" style="max-height: 300px; overflow-y: auto;">
                                <li><a class="dropdown-item" href="about.php">羅耀拉大樓[SL]</a></li>
                                <li><a class="dropdown-item" href="about1.html">利瑪竇大樓[LM]</a></li>
                            </ul>
                        </div>
                        <a href="service.php" class="nav-item nav-link ">我的預約</a>
                        <div class="nav-item dropdown">
                            <!-- 帶有圖片的 "我的帳號" 項目 -->
                            <a href="my_account.php" class="nav-item nav-link d-flex align-items-center active">
                                <img src="https://cdn-icons-png.flaticon.com/128/3033/3033143.png" alt="Account Icon" style="width: 25px; height: 25px; margin-right: 8px;">
                                <?php
                                 // 啟用 session
                                $account = isset($_SESSION['account']) ? $_SESSION['account'] : '未提供';
                                $link = mysqli_connect('localhost', 'root', '', 'reservation');
                                $sql = "SELECT name FROM user WHERE account='$account'";
                                $result = mysqli_query($link, $sql);
                                while ($row = mysqli_fetch_assoc($result)) 
                                {
                                    // 帳號和密碼正確
                                    $name = $row['name'];
                                    echo $name;
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="classroomDropdown" style="max-height: 300px; overflow-y: auto; border-radius: 8px; padding: 10px; width: 120px; min-width: 100px;">
                                <li>
                                    <!-- 登出項目 -->
                                    <a class="dropdown-item" href="login.html" style="font-size: 14px; padding: 10px 15px; color: #333; border-radius: 5px; transition: background-color 0.3s;">
                                        登出
                                    </a>
                                </li>
                            </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Navbar End -->

        <div class="hero-placeholder">
        </div>
        <!-- Hero Placeholder End -->
    
    <!-- Navbar & Hero End -->
</head>
<body>

    <!-- 預約表單 -->
    <form method="POST" action="">
        <h1>線上教室預約</h1>
        

        <label for="start_date" class="form-label">選擇開始日期：</label>
        <input type="date" id="start_date" name="start_date" required min="<?= date('Y-m-d') ?>">

        <label for="end_date" class="form-label">選擇結束日期：</label>
        <input type="date" id="end_date" name="end_date" required>

        <label class="form-label">選擇星期幾：</label>
        <div class="checkbox-group">
            <input type="checkbox" name="day_of_week[]" value="Monday">星期一
            <input type="checkbox" name="day_of_week[]" value="Tuesday">星期二
            <input type="checkbox" name="day_of_week[]" value="Wednesday">星期三
            <input type="checkbox" name="day_of_week[]" value="Thursday">星期四
            <input type="checkbox" name="day_of_week[]" value="Friday">星期五
            <input type="checkbox" name="day_of_week[]" value="Saturday">星期六
            <input type="checkbox" name="day_of_week[]" value="Sunday">星期日
        </div>

        <label class="form-label">選擇時間段：</label>
        <div class="checkbox-group">
            <input type="checkbox" name="time_slot[]" value="8:00-9:00">8:00-9:00
            <input type="checkbox" name="time_slot[]" value="9:00-10:00">9:00-10:00
            <input type="checkbox" name="time_slot[]" value="10:00-11:00">10:00-11:00
            <input type="checkbox" name="time_slot[]" value="11:00-12:00">11:00-12:00
            <input type="checkbox" name="time_slot[]" value="12:00-13:00">12:00-13:00
            <input type="checkbox" name="time_slot[]" value="13:00-14:00">13:00-14:00
            <input type="checkbox" name="time_slot[]" value="14:00-15:00">14:00-15:00
            <input type="checkbox" name="time_slot[]" value="15:00-16:00">15:00-16:00
            <input type="checkbox" name="time_slot[]" value="16:00-17:00">16:00-17:00
            <input type="checkbox" name="time_slot[]" value="17:00-18:00">17:00-18:00
        </div>

        <input type="button" value="確認送出" class="reservation-btn" onclick="confirmSubmit()">
    </form>

    <div class="reserved-slots">
        <h2>已預約的時間段：</h2>
        <ul>
            <?php foreach ($reservedSlots as $slot) { ?>
                <li><span><?= $slot['start_date'] ?> - <?= $slot['end_date'] ?></span>，星期：<?= $slot['day_of_week'] ?>，時段：<?= $slot['time_slot'] ?></li>
            <?php } ?>
        </ul>
    </div>

    <script>
        // 設置日期範圍
        window.onload = function() {
            var today = new Date();
            var maxDate = new Date();
            maxDate.setDate(today.getDate() + 29);
            
            // 格式化為 YYYY-MM-DD 格式
            var todayStr = today.toISOString().split('T')[0];
            var maxDateStr = maxDate.toISOString().split('T')[0];
            
            // 設定日期範圍
            document.getElementById("start_date").setAttribute("min", todayStr);
            document.getElementById("end_date").setAttribute("min", todayStr);
            document.getElementById("start_date").setAttribute("max", maxDateStr);
            document.getElementById("end_date").setAttribute("max", maxDateStr);
        };

            function confirmSubmit() {
            var confirmAction = confirm("確定要送出預約嗎？");
            if (confirmAction) {
                // 提交表單
                document.forms[0].submit();  // 提交表單
                // 跳轉到新頁面
                window.location.href = "service.php";  // 跳轉
            }
        }

    </script>

<!-- Footer Start -->
<div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <div class="footer-item d-flex flex-column">
                    <h4 class="text-white mb-3">輔仁大學教室預借系統</h4>
                        <div class="mb-2" style="overflow: hidden; line-height: 50px;">
                            <a href="#"><i class="fas fa-angle-right me-2"></i>執行團隊&nbsp;暴躁燒肉火車</a>
                        </div>
                        <div class="mb-2" style="margin-left:10px; overflow: hidden; line-height: 50px;">
                         
                            <a title="亞洲找車王" href="https://www.instagram.com/z.4ing/" target="_blank">
                                <img src="https://cdn-icons-png.flaticon.com/128/3621/3621435.png" style="width: 55px; height: 55px;" alt="Instagram Icon">
                            </a>
                          
                           <a title="陳培根" href="https://www.instagram.com/res._0704/">
                            <img src="https://cdn-icons-png.flaticon.com/128/3621/3621435.png" style="width: 55px; height: 55px;" alt="Instagram Icon">
                           </a>
                           <a title="辛蒂" href="https://www.instagram.com/wandering_my__day___/?locale=ru&hl=am-et">
                            <img src="https://cdn-icons-png.flaticon.com/128/3621/3621435.png" style="width: 55px; height: 55px;" alt="Instagram Icon">
                           </a>
                        </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="footer-item d-flex flex-column">
                    <h4 class="text-white mb-3">MY</h4>
                    <a href="service.html"><i class="fas fa-angle-right me-2"></i> 查詢預約紀錄</a>
                    <a href="my_account.html"><i class="fas fa-angle-right me-2"></i> 我的帳號</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->
    
    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-white"><a href="#"><i class="fas fa-copyright text-light me-2"></i>輔仁大學教室預借系統</a>, All right reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-white">
                    <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                    <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                    <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                    Designed By <a class="border-bottom" href="https://htmlcodex.com">暴躁燒肉火車</a> Distributed By <a class="border-bottom" href="https://themewagon.com">暴躁燒肉火車</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        

        <!-- Template Javascript -->
        <script src="js/main.js"></script>
        
    </body>

</html>
