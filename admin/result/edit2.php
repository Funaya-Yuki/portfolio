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

//サニタイズの処理
$token = bin2hex(openssl_random_pseudo_bytes(32));
$_SESSION['token'] = $token;

$id = $post['id'];
$old_year = $post['old_year'];
$year = $post['year'];
$month = $post['month'];

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "select * from rikishi_result where rikishi_id=:id and year=:year and month=:month";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':month', $month, PDO::PARAM_STR);
    $stmt->execute();
    $list = $stmt->fetch();

    if (empty($list)) {
        $_SESSION['msg']['error'] = 'その場所は初土俵より前の場所です。';
        header('Location: ./edit.php?id=' . $id . '&year=' . $old_year);
        exit;
    }
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
    <title>成績表の修正</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <?php
        echo '勝敗情報などを修正してください';
        echo '<br>';
        echo '<br>';

        unset($_SESSION['msg']['error']);
        ?>

        <form method="post" action="./edit_action.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="token" value="<?= $token ?>">
            勝敗
            <input type="text" name="result" value="<?= $list['result'] ?>">
            <br>
            <br>

            位　
            <input type="text" name="rank" value="<?= $list['rank'] ?>">
            <br>
            <br>

            <input type="submit" value="修正">
        </form>
        <br>
        <a href="./edit.php?id=<?= $id ?>&year=<?= $old_year ?>">戻る</a>
    </div>

</body>

</html>