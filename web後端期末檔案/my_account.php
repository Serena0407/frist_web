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
        /* 彈窗的樣式 */
        .modal {
            display: none; /* 默認隱藏 */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
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
                                    <?php while ($building = $buildings->fetch_assoc()): ?>
                                        <li>
                                            <a class="dropdown-item" href="about.php?building_id=<?= $building['building_id'] ?>">
                                                <?= $building['b_name'] ?>
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
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

 <!-- Manager Booking Records Start -->
<div class="container my-5">
    <h2 class="text-center mb-4">我的帳號</h2>
    <div class="container d-flex justify-content-center">
    <div class="card" style="margin-bottom: 100px; width: 800px;">
        <div class="card-body">
        <h4>姓名:
            <?php       
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
        </h4>

<!-- 插入分隔線 -->
<hr style="border: 1px solid #black; margin-top: 10px; margin-bottom: 20px;">

<div style="margin-top: 20px;">
    
        <div class="button-container">
        <button class="btn btn-danger" style="padding: 10px 15px; border-radius: 15px; width: 200px;" id="openModalEdit1">修改此帳號</button>
            <div id="myModalEdit1" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <?php
                        $account = isset($_SESSION['account']) ? $_SESSION['account'] : '未提供';

                        //Step 1
                        $link = mysqli_connect('localhost', 'root', '', 'reservation');
                        //Step 3
                        $sql = "select * from user where account = '$account'";
                        //echo $sql;
                        $result = mysqli_query($link, $sql);
                        // Step 4
                        if($row = mysqli_fetch_assoc($result))
                        {
                            $name = $row['name'];
                            $pw = $row['pw'];
                            //echo $title;
                        }
                    ?>
                    <!-- 修改表單 -->
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <label for="name">新名稱：</label>
                        <input type="text" id="name" name="name" required><br><br>
                        <label for="account">帳號：</label>
                        <input type="text" id="account" name="account" value = "<?php echo $account; ?>" readonly><br><br>
                        <label for="pw">密碼：</label>
                        <input type="password" id="pw" name="pw" required><br><br>
                        <button type="submit">提交修改</button>
                    </form>
                </div>
            </div>
            <form method="POST" onsubmit="return confirm('確定要刪除此帳號嗎？');">
            <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-warning" style="padding: 10px 15px; border-radius: 15px; width: 200px;">刪除當前帳號</button>
            </form>
            <?php
            // Step 1: 連接資料庫
             $link = mysqli_connect('localhost', 'root', '', 'reservation');
            // Step 2: 確認是否為 POST 請求
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                    session_start();
                    $account = isset($_SESSION['account']) ? $_SESSION['account'] : null;

                    if ($account) {
                        // Step 3: 執行刪除 SQL
                        $sql = "DELETE FROM user WHERE account = '$account'";
                        if (mysqli_query($link, $sql)) {
                            echo "<script>alert('刪除完成'); window.location.href = 'login.html';</script>";
                        } else {
                            echo "<script>alert('刪除失敗：" . mysqli_error($link) . "');</script>";
                        }
                    } else {
                        echo "<script>alert('未找到帳號，無法刪除');</script>";
                    }
                }
            }

            // Step 4: 關閉資料庫連接
            mysqli_close($link);
            ?>
            <button class="btn btn-success" style="padding: 10px 15px; border-radius: 15px; width: 200px;" id="openModalAdd1">新增帳號</button>
            <div id="myModalAdd1" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <!-- 新增表單 -->
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <label for="name">新名稱：</label>
                        <input type="text" id="name" name="name" required><br><br>
                        <label for="account">帳號：</label>
                        <input type="text" id="account" name="account" required pattern="\d{9}"><br><br>
                        <label for="pw">密碼：</label>
                        <input type="password" id="pw" name="pw" required><br><br>
                        <label for="email">信箱：</label>
                        <input type="email" id="email" name="email" required><br><br>
                        <button type="submit">新增帳號</button>
                    </form>
                </div>
            </div>
            <a href="login.html" class="btn btn-primary" style="display: block; padding: 10px 15px; border-radius: 15px; width: 200px; text-decoration: none; color: black;">
                登出此帳號
            </a>
        </div>
</div>
            </div>
        </div>
    </div>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 區分新增或修改的行為
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        // 新增管理者的邏輯
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
                echo "<script>alert('新增完成');</script>";
            } else {
                echo "新增失敗：" . mysqli_error($link);
            }

            mysqli_close($link);
        } else {
            echo "請填寫完整表單！";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // 修改管理者的邏輯
        $name = $_POST['name'] ?? null;
        $account = $_POST['account'] ?? null;
        $pw = $_POST['pw'] ?? null;

        if (!empty($name) && !empty($account) && !empty($pw)) {
            $link = mysqli_connect('localhost', 'root', '', 'reservation');

            if (!$link) {
                die("資料庫連接失敗：" . mysqli_connect_error());
            }

            $name = mysqli_real_escape_string($link, $name);
            $account = mysqli_real_escape_string($link, $account);
            $pw = mysqli_real_escape_string($link, $pw);

            $sql = "UPDATE user SET name = '$name', pw = '$pw' WHERE  account = '$account'";

            if (mysqli_query($link, $sql)) {
                echo "<script>alert('修改完成，請重新登入'); window.location.href = 'login.html';</script>";
            } else {
                echo "修改失敗：" . mysqli_error($link);
            }

            mysqli_close($link);
        } else {
            echo "請填寫完整表單！";
        }
    }
}
?>
<!-- JavaScript 控制模態框 -->
<script>
// 控制新增模態框
const modalAdd1 = document.getElementById("myModalAdd1");
const btnAdd1 = document.getElementById("openModalAdd1");
const spanAdd1 = modalAdd1?.getElementsByClassName("close")[0];

btnAdd1?.addEventListener("click", () => {
    modalAdd1.style.display = "block";
});

spanAdd1?.addEventListener("click", () => {
    modalAdd1.style.display = "none";
});

window.addEventListener("click", (event) => {
    if (event.target === modalAdd1) {
        modalAdd.style.display = "none";
    }
});

// 控制修改模態框 (如需要)
const modalEdit1 = document.getElementById("myModalEdit1");
const btnEdit1 = document.getElementById("openModalEdit1");
const spanEdit1 = modalEdit1?.getElementsByClassName("close")[0];

btnEdit1?.addEventListener("click", () => {
    modalEdit1.style.display = "block";
});

spanEdit1?.addEventListener("click", () => {
    modalEdit1.style.display = "none";
});

window.addEventListener("click", (event) => {
    if (event.target === modalEdit1) {
        modalEdit1.style.display = "none";
    }
});
</script>

<style>
    button .btn-primary a {
        color: black;  /* 默認文字顏色為黑色 */
        text-decoration: none;
    }

    button .btn-primary a:hover {
        color: #15B9D9; /* 懸停時文字顏色與按鈕背景顏色一致 */
    }
    .button-container {
        display: flex; /* 使用 Flexbox 佈局 */
        flex-wrap: wrap; /* 允許按鈕換行 */
        justify-content: center; /* 使按鈕在每行內居中 */
        gap: 20px; /* 按鈕之間的間隙 */
        max-width: 450px; /* 設置按鈕容器的最大寬度 */
        margin: 0 auto; /* 使按鈕容器居中 */
    }

    .btn {
        transition: none !important;  /* 禁用按鈕的過渡效果 */
    }

    .btn:hover, .btn:focus {
        outline: none;   /* 禁用焦點和懸停的邊框 */
    }
</style>
<!-- Manager Booking Records End -->

<!-- JavaScript -->





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
                    <a href="service.php"><i class="fas fa-angle-right me-2"></i> 查詢預約紀錄</a>
                    <a href="my_account.php"><i class="fas fa-angle-right me-2"></i> 我的帳號</a>
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