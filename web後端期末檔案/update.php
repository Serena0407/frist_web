<?php
// 連線資料庫
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'reservation';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連線失敗：" . $conn->connect_error);
}

// 更新教室資料
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $classroom_name = $_POST['classroom_name'];
    $capacity = intval($_POST['capacity']);
    $building_id = intval($_POST['building_id']);

    $stmt = $conn->prepare("UPDATE classrooms SET c_name = ?, c_capacity = ?, building_id = ? WHERE c_id = ?");
    $stmt->bind_param("siii", $classroom_name, $capacity, $building_id, $id);

    if ($stmt->execute()) {
        echo "教室資料已成功更新！";
        header("Location: classroom.php");
        exit();
    } else {
        echo "更新失敗：" . $stmt->error;
    }
}

// 檢查URL中的id參數
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $id = intval($_REQUEST['id']);

    $stmt = $conn->prepare("SELECT * FROM classrooms WHERE c_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $classroom = $result->fetch_assoc();
    } else {
        echo "該ID的教室資料未找到。";
        exit();
    }

    $buildings = $conn->query("SELECT * FROM buildings");
} else {
    echo "無效的ID參數。";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>修改教室</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2>修改教室</h2>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= $classroom['c_id'] ?>">
        <div class="mb-3">
            <label for="classroom_name" class="form-label">教室名稱</label>
            <input type="text" id="classroom_name" name="classroom_name" class="form-control" 
                   value="<?= $classroom['c_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="capacity" class="form-label">容納人數</label>
            <input type="number" id="capacity" name="capacity" class="form-control" 
                   value="<?= $classroom['c_capacity'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="building_id" class="form-label">所屬大樓</label>
            <select id="building_id" name="building_id" class="form-select" required>
                <?php while ($building = $buildings->fetch_assoc()): ?>
                    <option value="<?= $building['building_id'] ?>" 
                        <?= $building['building_id'] == $classroom['building_id'] ? 'selected' : '' ?>>
                        <?= $building['b_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="action" value="update" class="btn btn-primary">保存</button>
    </form>
</div>
</body>
</html>
