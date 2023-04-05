<?php
try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'select * from rikishi_profile';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    var_dump($e);
    exit;
}
?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>九重部屋の力士一覧</title>
    <link rel="stylesheet" href="rikishi.css">
</head>

<body>
    <div class="container">
        <h1>九重部屋の力士一覧</h1>
        <table>
            <thead>
                <tr class="background">
                    <th>力士名</th>
                    <th>年齢</th>
                    <th>身長(cm)</th>
                    <th>体重(kg)</th>
                    <th>出身地</th>
                    <th>得意技</th>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($list as $num => $rikishi) {
                    if ($num % 2 == 0) {  //1行ごとに背景色を変える
                        $class = 'class="background2"';
                    } else {
                        $class = 'class="background3"';
                    }
                ?>
                    <tr <?= $class ?>>
                        <td><a href="./result.php?id=<?= $rikishi['id'] ?>"><?= $rikishi['name'] ?></a></td>
                        <td><?= $rikishi['age'] ?></td>
                        <td><?= $rikishi['height'] ?></td>
                        <td><?= $rikishi['weight'] ?></td>
                        <td><?= $rikishi['barth_place'] ?></td>
                        <td><?= $rikishi['special_skill'] ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>

        </table>

    </div>

</body>

</html>