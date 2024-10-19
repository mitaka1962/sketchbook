<?php
header("Content-Type: application/json; charset=utf-8");
// クエリの取得 
if (isset($_GET['year'])) { $year = $_GET['year']; } else { $year = ""; }

$db_path = 'sketches.db';

if (!is_numeric($year)) {
    http_response_code(500);
    echo '{ "error": "Invalid value" }';
    return;
}

try {
    $mydb = new PDO('sqlite:'.$db_path);
    $mydb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_1 = "SELECT month, image_src, desc FROM sketches WHERE year = {$year} ORDER BY month DESC, day DESC, id DESC";
    $rows = $mydb->query($sql_1)->fetchAll(PDO::FETCH_ASSOC);

    // jsonで返す
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    $error_msg = array("error" => 'Connection failed: ' . $e->getMessage());
    echo json_encode($error_msg);
}
