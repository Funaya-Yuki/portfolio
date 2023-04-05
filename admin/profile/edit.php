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

//CSRF対策
$token = bin2hex(openssl_random_pseudo_bytes(32));
$_SESSION['token'] = $token;

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'select * from rikishi_profile where id=:id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $get['id'], PDO::PARAM_INT);
    $stmt->execute();
    $list = $stmt->fetch();
} catch (Exception $e) {
    header('Location:../../error/error.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>力士の修正画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <?php
        if (!empty($_SESSION['msg']['error'])) {
            echo $_SESSION['msg']['error'];
        } else {
            echo '力士の情報を修正してください';
            echo '<br>';
            echo '<br>';
        }

        $id = $get['id'];
        ?>

        <form method="post" action="./edit_action.php" class="form-inline">
            <div class="form-group">
                <input type="hidden" name="token" value="<?= $token ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                力士名
                <input type="text" name="name" value="<?= $list['name'] ?>" class="form-control"><br>
                年齢
                <input type="text" name="age" value="<?= $list['age'] ?>" class="form-control"><br>
                身長
                <input type="text" name="height" value="<?= $list['height'] ?>" class="form-control"><br>
                体重
                <input type="text" name="weight" value="<?= $list['weight'] ?>" class="form-control"><br>
                出身地
                <input type="text" name="barth_place" value="<?= $list['barth_place'] ?>" class="form-control"><br>
                得意技
                <input type="text" name="special_skill" value="<?= $list['special_skill'] ?>" class="form-control"><br>
                <input type="submit" value="修正" class="btn btn-primary">
        </form>
        <a href="../index.php" class="btn btn-default">戻る</a>
    </div>
    </div>

</body>

</html>