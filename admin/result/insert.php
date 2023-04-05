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

?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>成績の追加</title>
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
            echo '成績のデータ（西暦）を入力してください';
        }
        echo '<br>';
        echo '<br>';

        ?>

        <form method="post" action="./insert_action.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="token" value="<?= $token ?>">

            年　
            <input type="text" name="year">
            <br>
            <br>


            <input type="submit" value="決定">
        </form>
        <br>
        <a href="../index.php?id="<?= $id ?>>戻る</a>
    </div>

</body>

</html>