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

$id = $get['id'];

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "select * from rikishi_result where rikishi_id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}

foreach ($list as $v => $rikishi) {  //最後の年を代入
    $seireki = $rikishi['year'];
}

?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>成績の削除画面</title>
    <link rel="stylesheet" href="../../rikishi.css">
</head>

<body>
    <div class="container">
        <?= $seireki ?>年の行を削除してもよろしいですか。<br>
        <br>
        <br>

        <a href="./delete_action.php?id=<?= $id ?>&year=<?= $seireki ?>" class="btnBlue">削除</a>
        <a href="../result.php?id=<?= $id ?>" class="btn">戻る</a>

    </div>

</body>

</html>