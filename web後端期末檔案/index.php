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

// 處理刪除教室或大樓
if (isset($_GET['delete_classroom'])) {
    $id = $_GET['delete_classroom'];
    $conn->query("DELETE FROM classrooms WHERE c_id=$id");
    header("Location: classroom.php");
}

if (isset($_GET['delete_building'])) {
    $id = $_GET['delete_building'];
    $conn->query("DELETE FROM buildings WHERE building_id=$id");
    header("Location: classroom.php");
}

// 獲取大樓和教室清單
$buildings = $conn->query("SELECT * FROM buildings");
$classrooms = $conn->query("SELECT classrooms.*, buildings.b_name AS building_name FROM classrooms JOIN buildings ON classrooms.building_id = buildings.building_id ORDER BY c_id");
?>


<!DOCTYPE html>
<html lang="en">

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
            h5{
                align-items: center;
            }
            .breadcrumb {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .service .breadcrumb {
            margin: 20px 0;
        }
        .hero-placeholder { 
            height: 95px; 
            background: #ffffff; display: 
            flex; align-items: center; 
            justify-content: center; 
        }
        </style>
    </head>

    <body>
        <!-- Navbar & Hero Start -->
        <?php
            // 資料庫連線設定
            $host = 'localhost';
            $username = 'root';
            $password = ''; // 根據你的設定修改
            $dbname = 'reservation';
            $conn = new mysqli($host, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("連線失敗：" . $conn->connect_error);
            }

            // 檢查是否有 building_id 作為篩選條件
            $whereClause = '';
            if (isset($_GET['building_id']) && is_numeric($_GET['building_id'])) {
                $building_id = $_GET['building_id'];
                $whereClause = "WHERE classrooms.building_id = $building_id";
            }

            // 獲取大樓清單（供 Navbar 使用）
            $buildings = $conn->query("SELECT * FROM buildings");

            // 獲取教室清單（根據 building_id 過濾）
            $classrooms = $conn->query(
                "SELECT classrooms.*, buildings.b_name AS building_name 
                FROM classrooms 
                JOIN buildings ON classrooms.building_id = buildings.building_id 
                $whereClause"
            );
        ?>
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
                                    可預約教室一覽
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="classroomDropdown" style="max-height: 300px; overflow-y: auto;">
                                    <?php while ($building = $buildings->fetch_assoc()): ?>
                                        <li>
                                            <a class="dropdown-item" href="index.php?building_id=<?= $building['building_id'] ?>">
                                                <?= $building['b_name'] ?>
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                            <div class="nav-item dropdown">
                                <a href="login.html" class="nav-item nav-link">登入</a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
    
        <!-- Navbar End -->

        <!-- Carousel Start -->
        <div class="header-carousel owl-carousel">
            <div class="header-carousel-item">
                <img src="https://www.fju.edu.tw/showImg/subLabelImg/1_1_52_1.png" class="img-fluid w-100" alt="Image">
                <div class="carousel-caption">
                    <div class="carousel-caption-content p-3">
                        <h5 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">FJU | Classroom Reservation System
                        </h5>
                        <h1 class="display-1 text-capitalize text-white mb-4">輔仁大學教室預借系統</h1>
                        <p class="mb-5 fs-5 animated slideInDown">輔仁大學成立於1961年，是台灣知名的天主教大學，提供多元化的學科與國際化的學習環境。學校注重人文素養與社會責任，培養具有全球視野的優秀人才，深受學生與家長的喜愛。
                        </p>
                        <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="login.html">線上預約</a>
                    </div>
                </div>
            </div>
            <div class="header-carousel-item">
                <img src="https://i.ytimg.com/vi/Jsd86NnoOIU/maxresdefault.jpg" class="img-fluid w-100" alt="Image">
                <div class="carousel-caption">
                    <div class="carousel-caption-content p-3">
                        <h5 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">FJU | Classroom Reservation System
                        </h5>
                        <h1 class="display-1 text-capitalize text-white mb-4">輔仁大學教室預借系統</h1>
                        <p class="mb-5 fs-5">新理工大樓於2024年落成完工，這棟大樓將提供現代化的實驗室及教學空間，提升校內理工學科的教學與研究環境。
                        </p>
                        <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="login.html">線上預約</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->


       

        <!-- Services Start -->
        <div class="container-fluid service py-5" style="margin-top: 3%; margin-bottom: 3%;">
            
            <div class="views-exposed-form" id="searchResult" style="margin-bottom: 50px; text-align: center;">
                <div class="views-exposed-widgets clearfix">
                    <div id="edit-title-wrapper" class="views-exposed-widget views-widget-filter-title">
                        <label for="edit-title"><font color="black"><b>教室查詢</b></font></label>
                        <input type="text" id="edit-title" name="title" value="" size="30" maxlength="128" class="form-text" style="border-radius: 10px;">
                        <input type="submit" id="edit-submit-clone-of-eq" name="" value="Search" class="form-submit btn-primary text-white" style="border-radius: 10px;">
                    </div>
                </div>
            </div>

            <h2 class="text-primary text-center mb-4">教室清單</h2>
            <div class="table-responsive" style="max-width: 80%; margin: auto">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>名稱</th>
                            <th>容納人數</th>
                            <th>大樓</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($classroom = $classrooms->fetch_assoc()): ?>
                            <tr>
                                <td><?= $classroom['c_id'] ?></td>
                                <td><?= $classroom['c_name'] ?></td>
                                <td><?= $classroom['c_capacity'] ?></td>
                                <td><?= $classroom['building_name'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        


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
                              
                               <a title="陳培根" href="https://www.instagram.com/res._0704/" target="_blank">
                                <img src="https://cdn-icons-png.flaticon.com/128/3621/3621435.png" style="width: 55px; height: 55px;" alt="Instagram Icon">
                               </a>
                               <a title="辛蒂" href="https://www.instagram.com/wandering_my__day___/?locale=ru&hl=am-et" target="_blank">
                                <img src="https://cdn-icons-png.flaticon.com/128/3621/3621435.png" style="width: 55px; height: 55px;" alt="Instagram Icon">
                               </a>
                            </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-3">MY</h4>
                        <a href="index.php"><i class="fas fa-angle-right me-2"></i> 可預約教室一覽</a>
                        <a href="login.html"><i class="fas fa-angle-right me-2"></i> 登入</a>
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