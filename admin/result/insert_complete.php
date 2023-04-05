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
?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>成績の追加完了の画面</title>
</head>

<body>
    <div class="container">
        追加しました。<br>
        <br>
        <a href="../result.php?id=<?= $id ?>">力士の成績一覧へ</a>

    </div>

</body>

</html>