<?php
header("Content-Type: application/json; charset=utf-8");
// クエリの取得 
if (isset($_GET['year'])) { $year = $_GET['year']; } else { $year = ""; }

$db_path = 'sketches.db';
try {
    $mydb = new PDO('sqlite:'.$db_path);
    $mydb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_1 = "SELECT month, image_src, desc FROM sketches WHERE year = {$year} ORDER BY month DESC, day DESC, id DESC";
    $rows = $mydb->query($sql_1)->fetchAll();

    // jsonで返す
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Connection failed: '.$e->getMessage();
}