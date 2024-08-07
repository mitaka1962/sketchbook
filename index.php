<?php
$json_text = "[";
$err_text = "";
// スケッチデータの年のリストを取得し、jsonに整形
$db_path = 'sketches.db';
try {
    $mydb = new PDO('sqlite:'.$db_path);
    $mydb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_1 = "SELECT DISTINCT year FROM sketches ORDER BY year ASC";
    $rows = $mydb->query($sql_1)->fetchAll();
    // 末尾のカンマはつけない
    for ($i = 0; $i < count($rows); $i++) { 
        if ($i != 0) $json_text .= ",";
        $json_text .= $rows[$i]['year'];
    }
} catch (PDOException $e) {
    $err_text = 'Connection failed: '.$e->getMessage();
}
$json_text .= "]";
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>スケッチブック</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2, minimum-scale=1, user-scalable=yes">
        <link rel="stylesheet" href="sketchbook_style.css" media="all">
        <!-- luminous lightbox css -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/luminous-lightbox@2.4.0/dist/luminous-basic.min.css">
    </head>
    <body>
        <div class="wrapper">
            <div class="container">
                <div class="main">
                    <h1 class="main_heading">スケッチブック</h1>
                    <?php
                    // データベースと接続失敗の際、エラー出力
                    if ($err_text) echo $err_text;
                    ?>
                    <div class="sketchbook_main">
                        <div class="sketchbook_year_container">
                            <button id="sketchbook_previous_year_btn">&lt;</button>
                            <h2 id="sketchbook_year"></h2>
                            <button id="sketchbook_next_year_btn">&gt;</button>
                        </div>
                        <div id="sketchbook_grid"></div>
                    </div>                    
                </div>
            </div>
        </div>
        <?php
        // <script>でjsonを埋め込み
        echo "<script id=\"sketchbook_years_data\" type=\"application/json\">{$json_text}</script>";
        ?>
        <!-- luminous lightbox js -->
        <script src="https://cdn.jsdelivr.net/npm/luminous-lightbox@2.4.0/dist/luminous.min.js"></script>
        <script src="sketchbook_fetch.js"></script>
    </body>
</html>