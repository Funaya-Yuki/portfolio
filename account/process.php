<?php
session_start();
session_regenerate_id(true);

$post = array();

//サニタイズの処理
foreach ($_POST as $k => $v) {
    $post[$k] = htmlspecialchars($v);
}

//CSRF対策
if (!isset($_SESSION['token']) || $_SESSION['token'] !== $post['token']) {
    $_SESSION['msg']['error'] = '不正な処理が行われました。';
    header('Location: ./');
    exit;
}

if (empty($post['account'])) {
    $_SESSION['msg']['error'] = 'アカウント名を入力してください。';
    header('Location: ./');
    exit;
}

if (empty($post['email'])) {
    $_SESSION['msg']['error'] = 'メールアドレスを入力してください。';
    header('Location: ./');
    exit;
}

if (empty($post['pass'])) {
    $_SESSION['msg']['error'] = 'パスワードを入力してください。';
    header('Location: ./');
    exit;
}

$hash_pass = password_hash($post['pass'], PASSWORD_DEFAULT);  //パスワードの暗号化

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //同じアカウントが登録されてないかチェック
    $sql = 'select * from administrators where account_name=:account_name and email=:email';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':account_name', $post['account'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $post['email'], PDO::PARAM_STR);
    $stmt->execute();
    $list = $stmt->fetch();

    if (!empty($list) && password_verify($post['pass'], $list['pass'])) {
        $_SESSION['msg']['error'] = 'このアカウントはすでに登録されています。';
        header('Location: ./');
        exit;
    }

    //アカウントを登録
    $sql = 'insert into administrators(account_name, email, pass)values(:account_name, :email, :pass)';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':account_name', $post['account'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $post['email'], PDO::PARAM_STR);
    $stmt->bindParam(':pass', $hash_pass, PDO::PARAM_STR);
    $stmt->execute();

    unset($_SESSION['msg']['error']);

    header('Location: ./complete.php');
    exit;
} catch (Exception $e) {
    var_dump($e);
    exit;
}
