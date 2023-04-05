<?php
session_start();
session_regenerate_id(true);

//サニタイズの処理
$post = array();
foreach ($_POST as $k => $v) {
    $post[$k] = htmlspecialchars($v);
}

//CSRF対策
if (!isset($_SESSION['token']) || $_SESSION['token'] !== $post['token']) {
    $_SESSION['msg']['error'] = '不正な処理が行われました。';
    header('Location: ./');
    exit;
}

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'select * from administrators where account_name=:user_name or email=:user_name';  //ログインのチェック
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':user_name', $post['user_name'], PDO::PARAM_STR);
    $stmt->execute();
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($list) || !password_verify($post['pass'], $list[0]['pass'])) {
        $_SESSION['msg']['error'] = 'アカウント名、メールアドレス、またはパスワードが違います。';
        header('Location: ./index.php');
        exit;
    }

    unset($_SESSION['msg']['error']);

    $_SESSION['user'] = 1;  //ログイン状態

    header('Location: ../admin/index.php');
    exit;
} catch (Exception $e) {
    header('Location: ../error/error.php');
    exit;
}
