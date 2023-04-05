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

//CSRF対策
$token = bin2hex(openssl_random_pseudo_bytes(32));
$_SESSION['token'] = $token;

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'select year from rikishi_result where rikishi_id=:id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        if (!empty($_SESSION['msg']['error'])) {
            echo $_SESSION['msg']['error'];
        } else {
            echo '修正する場所を選んでください';
        }
        echo '<br>';
        echo '<br>';
        $year = $get['year'];
        foreach ($list as $v => $seireki) {  //最後の年を代入
            $num = $seireki['year'];
        }

        ?>

        <form method="post" action="./edit2.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="old_year" value="<?= $year ?>">
            <input type="hidden" name="token" value="<?= $token ?>">
            西暦
            <select name="year">
                <?php for ($i = $year; $i <= $num; $i++) {
                    echo "<option value=$i>{$i}</option>";
                }
                ?>
            </select>
            <br>
            <br>

            場所
            <select name="month">
                <option value="1月場所">1月場所</option>
                <option value="3月場所">3月場所</option>
                <option value="5月場所">5月場所</option>
                <option value="7月場所">7月場所</option>
                <option value="9月場所">9月場所</option>
                <option value="11月場所">11月場所</option>
            </select>
            <br>
            <br>

            <input type="submit" value="決定">
        </form>
        <br>
        <a href="../result.php?id=<?= $id ?>">戻る</a>
    </div>

</body>

</html>