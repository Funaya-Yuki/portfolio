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

$id = $post['id'];

if (!is_numeric($post['year'])) {
    $_SESSION['msg']['error'] = '西暦は半角数字でお願いします。';
    header('Location: ./insert.php?id='. $id);
    exit;
}

if ($post['year'] < 2019) {  //和暦の計算
    $num = $post['year'] - 1988;
    $wareki = '平成' . $num . '年';
} elseif ($post['year'] == 2019) {
    $wareki = '令和元年';
} else {
    $num = $post['year'] - 2018;
    $wareki = '令和' . $num . '年';
}

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //追加処理
    for ($i = 1; $i <= 11; $i = $i + 2) {  //成績を1年分追加
        $j = "{$i}月場所";
        $k = '';  //勝敗と位は空とする
        $l = '';
        $sql = 'insert into rikishi_result(rikishi_id, year, wareki, month, result, rank)values(:rikishi_id, :year, :wareki, :month, :result, :rank)';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':rikishi_id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':year', $post['year'], PDO::PARAM_INT);
        $stmt->bindParam(':wareki', $wareki, PDO::PARAM_STR);
        $stmt->bindParam(':month', $j, PDO::PARAM_STR);
        $stmt->bindParam(':result', $k, PDO::PARAM_STR);
        $stmt->bindParam(':rank', $l, PDO::PARAM_STR);
        $stmt->execute();
    }

    header('Location: ./insert_complete.php?id='. $id);
    exit;
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}
