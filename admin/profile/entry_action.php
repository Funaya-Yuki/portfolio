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

$_SESSION['post'] = $post;

//CSRF対策
if (!isset($_SESSION['token']) || $_SESSION['token'] !== $post['token']) {
    $_SESSION['msg']['error'] = '不正な処理が行われました。';
    header('Location: ./entry.php');
    exit;
}

if (!is_numeric($post['age']) || !is_numeric($post['height']) || !is_numeric($post['weight'])) {
    $_SESSION['msg']['error'] = '年齢、身長、体重は半角数字でお願いします。';
    header('Location: ./entry.php');
    exit;
}

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'select * from rikishi_profile where name=:name';  //同じ力士名の力士がいないかチェック
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':name', $post['name'], PDO::PARAM_STR);
    $stmt->execute();
    $list = $stmt->fetch();

    if (!empty($list)) {
        $_SESSION['msg']['error'] = '同じ力士名は追加できません';
        header('Location: ./entry.php');
        exit;
    }

    //力士の追加
    $sql = 'insert into rikishi_profile(name, age, height, weight, barth_place, special_skill)values(:name, :age, :height, :weight, :barth_place, :special_skill)';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':name', $post['name'], PDO::PARAM_STR);
    $stmt->bindParam(':age', $post['age'], PDO::PARAM_INT);
    $stmt->bindParam(':height', $post['height'], PDO::PARAM_INT);
    $stmt->bindParam(':weight', $post['weight'], PDO::PARAM_INT);
    $stmt->bindParam(':barth_place', $post['barth_place'], PDO::PARAM_STR);
    $stmt->bindParam(':special_skill', $post['special_skill'], PDO::PARAM_STR);
    $stmt->execute();

    unset($_SESSION['msg']['error']);

    header('Location: ./entry_complete.php');
    exit;
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}
