<?php
session_start();
session_regenerate_id(true);

if (empty($_SESSION['user'])) {  //ログイン状態であるか確認
    header('Location: ../../login/index.php');
    exit;
}

//サニタイズの処理
$post = array();
foreach ($_POST as $k => $v) {
    $post[$k] = htmlspecialchars($v);
}

//CSRF対策
if (!isset($_SESSION['token']) || $_SESSION['token'] !== $post['token']) {
    $_SESSION['msg']['error'] = '不正な処理が行われました。';
    header('Location: ./edit.php');
    exit;
}

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //修正処理
    $sql = 'update rikishi_result set result=:result, rank=:rank where rikishi_id=:id and year=:year and month=:month';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':result', $post['result'], PDO::PARAM_STR);
    $stmt->bindParam(':rank', $post['rank'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $post['id'], PDO::PARAM_INT);
    $stmt->bindParam(':year', $post['year'], PDO::PARAM_INT);
    $stmt->bindParam(':month', $post['month'], PDO::PARAM_STR);
    $stmt->execute();

    $id = $post['id'];
    header('Location: ./edit_complete.php?id=' . $id);
    exit;
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}
