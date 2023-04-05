<?php
session_start();
session_regenerate_id(true);

if (empty($_SESSION['user'])) {  //ログイン状態であるか確認
    header('Location: ../../login/index.php');
    exit;
}

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
    <title>力士の追加画面</title>
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
            echo '追加する力士の情報を入力してください';
            echo '<br>';
            echo '<br>';
        }
        ?>
        <form method="post" action="./entry_action.php" class="form-inline">
            <input type="hidden" name="token" value="<?= $token ?>">
            <div class="form-group">
                力士名
                <input type="text" name="name" value="<?= isset($_SESSION['post']['name']) ? $_SESSION['post']['name'] : '' ?>" class="form-control"><br>
                年齢
                <input type="text" name="age" value="<?= isset($_SESSION['post']['age']) ? $_SESSION['post']['age'] : '' ?>" class="form-control"><br>
                身長
                <input type="text" name="height" value="<?= isset($_SESSION['post']['height']) ? $_SESSION['post']['height'] : '' ?>" class="form-control"><br>
                体重
                <input type="text" name="weight" value="<?= isset($_SESSION['post']['weight']) ? $_SESSION['post']['weight'] : '' ?>" class="form-control"><br>
                出身地
                <input type="text" name="barth_place" value="<?= isset($_SESSION['post']['barth_place']) ? $_SESSION['post']['barth_place'] : '' ?>" class="form-control"><br>
                得意技
                <input type="text" name="special_skill" value="<?= isset($_SESSION['post']['special_skill']) ? $_SESSION['post']['special_skill'] : '' ?>" class="form-control"><br>

                <input type="submit" value="追加" class="btn btn-primary">
        </form>

        <a href="../index.php" class="btn btn-default">戻る</a>
    </div>
    </div>

</body>

</html>