<?php
session_start();
session_regenerate_id(true);

if (empty($_SESSION['user'])) {  //ログイン状態であるか確認
    header('Location: ../../login/index.php');
    exit;
}

//サニタイズの処理
$get = array();
foreach ($_GET as $k => $v) {
    $get[$k] = htmlspecialchars($v);
}

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    for ($i = 1; $i <= 11; $i = $i + 2) {  //成績を1年分追加
        $j = "{$i}月場所";
        $k = '';  //勝敗と位は空とする
        $l = '';
        $sql = "insert into rikishi_result(rikishi_id, year, wareki, month, result, rank)values(:rikishi_id, :year, :wareki, :month, :result, :rank)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':rikishi_id', $get['id'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $get['year'], PDO::PARAM_INT);
        $stmt->bindParam(':wareki', $get['wareki'], PDO::PARAM_STR);
        $stmt->bindParam(':month', $j, PDO::PARAM_STR);
        $stmt->bindParam(':result', $k, PDO::PARAM_STR);
        $stmt->bindParam(':rank', $l, PDO::PARAM_STR);
        $stmt->execute();
    }
    $id = $get['id'];
    header('Location: ./entry_complete.php?id=' . $id);
    exit;
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}
