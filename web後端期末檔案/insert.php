<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>輔仁大學教室預借系統</title>
    <link rel="icon" href="http://home.lib.fju.edu.tw/TC/sites/default/files/shield-FJCULIB-FJCU.png" type="image/png">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">


    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <style>
        .hero-placeholder {
            height: 95px; /* 可調整佔位符的高度 */
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="classroomDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            管理教室
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="classroomDropdown" style="max-height: 300px; overflow-y: auto;">
                            <li><a class="dropdown-item" href="about.html">羅耀拉大樓[SL]</a></li>
                            <li><a class="dropdown-item" href="about1.html">利瑪竇大樓[LM]</a></li>
                        </ul>
                    </div>
                    <a href="service.html" class="nav-item nav-link">管理預約</a>
                    <div class="nav-item dropdown">
                            <a href="my_account.html" class="nav-item nav-link">我的帳號</a>
                            <ul class="dropdown-menu" aria-labelledby="classroomDropdown" style="max-height: 300px; overflow-y: auto;">
                                <li><a class="dropdown-item" href="my_account.html"><img src="https://cdn-icons-png.flaticon.com/128/3033/3033143.png" style="width: 38px; height:38px;"></a></li>
                                <li class="breadcrumb-divider" style="border-top: 1px solid #ccc; "></li>
                                <li><a class="dropdown-item" href="login.html">登出</a></li>
                            </ul>
                        </div>
                </div>
            </div>
        </nav>
    </div>     

    <div class="hero-placeholder"></div>

    
    <?php

// 連線資料庫
$host = 'localhost';
$username = 'root';
$password = ''; // 根據你的設定修改
$dbname = 'reservation';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連線失敗：" . $conn->connect_error);
}

// 啟用錯誤報告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_classroom') {
        // 驗證輸入
        if (!isset($_POST['classroom_name'], $_POST['capacity']) || empty($_POST['classroom_name']) || (int)$_POST['capacity'] <= 0) {
            die("教室名稱和容納量為必填！");
        }
        $classroom_name = $_POST['classroom_name'];
        $capacity = (int)$_POST['capacity'];

        if (!isset($_POST['building_id']) || $_POST['building_id'] === '') {
            die("請選擇或新增一棟大樓！");
        }

        if ($_POST['building_id'] === 'new') {
            if (!isset($_POST['new_building_name']) || empty($_POST['new_building_name'])) {
                die("請提供大樓名稱！");
            }
            $new_building_name = $_POST['new_building_name'];

            // 新增大樓
            $stmt = $conn->prepare("INSERT INTO buildings (b_name) VALUES (?)");
            if (!$stmt) {
                die("預備失敗: " . $conn->error);
            }
            $stmt->bind_param('s', $new_building_name);
            if (!$stmt->execute()) {
                die("新增大樓失敗: " . $stmt->error);
            }
            $building_id = $stmt->insert_id; // 獲取新大樓 ID
            $stmt->close();
        } else {
            $building_id = (int)$_POST['building_id'];

            // 檢查 building_id 是否有效
            $stmt = $conn->prepare("SELECT COUNT(*) FROM buildings WHERE building_id = ?");
            if (!$stmt) {
                die("檢查失敗: " . $conn->error);
            }
            $stmt->bind_param('i', $building_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            if ($count == 0) {
                die("無效的大樓 ID: $building_id");
            }
            $stmt->close();
        }

        // 新增教室
        $stmt = $conn->prepare("INSERT INTO classrooms (c_name, c_capacity, building_id) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("預備失敗: " . $conn->error);
        }
        $stmt->bind_param('sii', $classroom_name, $capacity, $building_id);
        if (!$stmt->execute()) {
            die("新增教室失敗: " . $stmt->error);
        }
        $stmt->close();

        // 成功新增後跳轉
        header("Location: classroom.php");
        exit;
    }
}

// 獲取大樓列表
$buildings = $conn->query("SELECT * FROM buildings");
if (!$buildings) {
    die("查詢失敗: " . $conn->error);
}
?>








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