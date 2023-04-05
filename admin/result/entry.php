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

    $sql = "select * from rikishi_result join rikishi_profile on rikishi_result.rikishi_id=rikishi_profile.id where rikishi_id=:id";
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
    $wareki = $rikishi['wareki'];
}

$seireki = $seireki + 1;

if ($seireki < 2019) {  //和暦の計算
    $num = $seireki - 1988;
    $wareki = '平成' . $num . '年';
} elseif ($seireki == 2019) {
    $wareki = '令和元年';
} else {
    $num = $seireki - 2018;
    $wareki = '令和' . $num . '年';
}
?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>成績の追加画面</title>
    <link rel="stylesheet" href="../../rikishi.css">
</head>

<body>
    <div class="container">
        次の行を追加しますか。<br>
        <br>
        <table>
            <thead>
                <tr class="background">
                    <th></th>
                    <th></th>
                    <th>1月場所</th>
                    <th>3月場所</th>
                    <th>5月場所</th>
                    <th>7月場所</th>
                    <th>9月場所</th>
                    <th>11月場所</th>
                </tr>
            </thead>

            <body>
                <tr class="background2">
                    <td><?= $seireki ?>年</td>
                    <td>勝敗</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <tr class="background2">
                    <td><?= $wareki ?></td>
                    <td>位</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </body>
        </table>

        <br>
        <br>

        <a href="./entry_action.php?id=<?= $id ?>&year=<?= $seireki ?>&wareki=<?= $wareki ?>" class="btnBlue">追加</a>
        <a href="../result.php?id=<?= $id ?>" class="btn">戻る</a>

    </div>

</body>

</html>