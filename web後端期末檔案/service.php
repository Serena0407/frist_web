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

// 處理刪除
if (isset($_GET['delete_reservation'])) {
    $id = $_GET['delete_reservation'];
    $conn->query("DELETE FROM reservations WHERE r_id=$id");
    header("Location: service.php");
}

// 獲取清單
$query = ("SELECT r_date, r_time, classrooms.c_name AS classroom_name, r_state FROM reservations JOIN classrooms ON reservations.c_id = classrooms.c_id");
$reserve = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>我的預約｜輔仁大學教室預約系統</title>
    <link rel="icon" href="http://home.lib.fju.edu.tw/TC/sites/default/files/shield-FJCULIB-FJCU.png" type="image/png">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .hero-placeholder { height: 95px; background: #ffffff; display: flex; align-items: center; justify-content: center; }
        .navbar { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <!-- Navbar & Hero Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 px-lg-5 py-3 py-lg-0">
            <a href="about.html" class="navbar-brand p-0"><h1 class="text-primary m-0"><b>輔仁大學教室預借系統</b></h1></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"><span class="fa fa-bars"></span></button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="classroomDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">教室預約系統</a>
                        <ul class="dropdown-menu" aria-labelledby="classroomDropdown" style="max-height: 300px; overflow-y: auto;">
                            <li><a class="dropdown-item" href="about.html">羅耀拉大樓[SL]</a></li>
                            <li><a class="dropdown-item" href="about1.html">利瑪竇大樓[LM]</a></li>
                        </ul>
                    </div>
                    <a href="service.html" class="nav-item nav-link active">我的預約</a>
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
    <!-- Navbar End -->

    <div class="hero-placeholder"></div>

    <div class="container my-5">
        <h2 class="text-center mb-4">查詢預約記錄</h2>
        <div class="card" style="margin-bottom: 100px;">
            <div class="card-body">
                <form id="searchForm">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="searchKeyword" class="form-label">查詢關鍵字</label>
                            <input type="text" class="form-control" id="searchKeyword" placeholder="輸入關鍵字">
                        </div>
                        <div class="col-md-4">
                            <label for="startDate" class="form-label">開始日期</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-4">
                            <label for="endDate" class="form-label">結束日期</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">查詢</button>
                </form>
                <div class="mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>預約日期</th>
                                <th>預約時間</th>
                                <th>教室名稱</th>
                                <th>審核狀態</th>
                                <th>修改預約時間/教室</th>
                                <th>刪除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $reserve->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['r_date'] ?></td>
                                    <td><?= $row['r_time'] ?></td>
                                    <td><?= $row['classroom_name'] ?></td>
                                    <td><?= $row['r_state'] ?></td>
                                    <td>
                                        <a href="update.php?id=<?= $classroom['c_id'] ?>" class="btn btn-warning btn-sm">修改</a>
                                    </td>
                                    <td>
                                        <a href="service.php?delete_reservation=<?= $row['r_id'] ?>" 
                                            class="btn btn-danger btn-sm" 
                                            onclick="return confirm('確定刪除?')">刪除</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmLabel">確認刪除</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">您確定要刪除此預約記錄嗎？</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">確定刪除</button>
                    </div>
                </div>
            </div>
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
            <!-- Add these scripts at the end of the body, after jQuery -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
      
</body>
</html>
