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
                                    <?php while ($building = $buildings->fetch_assoc()): ?>
                                        <li>
                                            <a class="dropdown-item" href="classroom.php?building_id=<?= $building['building_id'] ?>">
                                                <?= $building['b_name'] ?>
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                    </div>
                    <a href="manager.html" class="nav-item nav-link">管理預約</a>
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

    <div class="container py-5" style="max-width: 800px; margin-top:70px; margin-bottom:70px;">
    <h2 class="text-primary text-center mb-4">新增教室</h2>
<form action="insert.php" method="POST" class="mb-5">
    <div class="mb-3">
        <input type="text" name="classroom_name" class="form-control" placeholder="教室名稱" required>
    </div>
    <div class="mb-3">
        <input type="number" name="capacity" class="form-control" placeholder="容納人數" required>
    </div>
    <div class="mb-3">
        <label>大樓</label>
        <select name="building_id" id="buildingSelect" class="form-select">
            <option value="" disabled selected>選擇大樓</option>
            <?php while ($building = $buildings->fetch_assoc()): ?>
                <option value="<?= $building['building_id'] ?>"><?= $building['b_name'] ?></option>
            <?php endwhile; ?>
            <option value="new">新增大樓</option>
        </select>
    </div>
    <div class="mb-3" id="newBuildingField" style="display: none;">
        <input type="text" name="new_building_name" class="form-control" placeholder="新大樓名稱">
    </div>
    <button type="submit" name="action" value="add_classroom" class="btn btn-primary w-100">新增教室</button>
</form>
<script>
    document.getElementById('buildingSelect').addEventListener('change', function () {
        const newBuildingField = document.getElementById('newBuildingField');
        newBuildingField.style.display = this.value === 'new' ? 'block' : 'none';
    });
</script>

    <!-- 教室清單 -->
    <h2 class="text-primary text-center mb-4">教室清單</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名稱</th>
                    <th>容納人數</th>
                    <th>大樓</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($classroom = $classrooms->fetch_assoc()): ?>
                    <tr>
                        <td><?= $classroom['c_id'] ?></td>
                        <td><?= $classroom['c_name'] ?></td>
                        <td><?= $classroom['c_capacity'] ?></td>
                        <td><?= $classroom['building_name'] ?></td>
                        <td>
                            <a href="update.php?id=<?= $classroom['c_id'] ?>" class="btn btn-warning btn-sm">修改</a>
                            <a href="classroom.php?delete_classroom=<?= $classroom['c_id'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('確定刪除?')">刪除</a>
                        </td>
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