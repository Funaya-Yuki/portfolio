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
    header('Location: ../../login/index.php');
    exit;
}

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'delete from rikishi_profile where id=:id';  //削除処理
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $post['id'], PDO::PARAM_INT);
    $stmt->execute();

    header('Location: ./delete_complete.php');
    exit;
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}
