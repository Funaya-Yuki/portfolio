<?php
session_start();
session_regenerate_id(true);

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
    <title>アカウント登録</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <?php
        if (!empty($_SESSION['msg']['error'])) {
            echo $_SESSION['msg']['error'];
        }
        ?>
        <form method="post" action="./process.php" class="form-inline">
            <div class="form-group">
                <input type="hidden" name="token" value="<?= $token ?>">
                アカウント名
                <input type="text" name="account" class="form-control"><br>
                メールアドレス
                <input type="text" name="email" class="form-control"><br>
                パスワード
                <input type="password" name="pass" class="form-control"><br>

                <input type="submit" value="登録">

        </form>
    </div>

</body>

</html>