<?php
session_start();
session_regenerate_id(true);

unset($_SESSION['msg']['error']);

if (empty($_SESSION['user'])) {  //ログイン状態であるか確認
    header('Location: ../login/index.php');
    exit;
}

//サニタイズの処理
$get = array();
foreach ($_GET as $k => $v) {
    $get[$k] = htmlspecialchars($v);
}

$id = $get['id'];
$flag = 1;  //新しい行かを調べるための変数
$cnt = 0;  //初土俵の場所を調べるための変数
$rank = array();  //位を保存するための配列

try {
    $dsn = 'mysql:dbname=osumosan_list;host=localhost;charset=utf8';
    $dbh = new PDO($dsn, 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "select * from rikishi_result join rikishi_profile on rikishi_result.rikishi_id=rikishi_profile.id where rikishi_id=$id";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    header('Location:../error/error.php');
    exit;
}

if (empty($list)) {  //もし、成績のデータがない場合
    header('Location: ./result/insert.php?id='. $id);
    exit;
}

$class = 'background2';  //背景色
$class2 = 'font';  //文字を太くするためのクラス
?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>九重部屋の力士の成績一覧</title>
    <link rel="stylesheet" href="../rikishi.css">
</head>

<body>
    <div class="container">
        <h1><?= $list[0]['name'] ?>のこれまでの成績</h1>
        <a href="./result/entry.php?id=<?= $id ?>">追加</a>
        <a href="./result/edit.php?id=<?= $id ?>&year=<?= $list[0]['year'] ?>">修正</a>
        <a href="./result/delete.php?id=<?= $id ?>">削除</a>
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

            <tbody>
                <?php foreach ($list as $v => $rikishi) {
                    //勝敗を表示していく
                    if ($flag == 1) {  //新しい行の場合
                        echo '<tr class=' . $class . '>';
                        echo "<td class=" . $class2 . ">" . $rikishi['year'] . '年</td>';
                        echo "<td class=" . $class2 . ">勝敗</td>";
                    }
                    if ($v == 0) {  //初土俵のときで、最初のデータを表示するとき
                        for ($i = 1; $i <= 11; $i = $i + 2) {  //1月場所から11月場所までを調べる
                            if ($rikishi['month'] == $i . '月場所') {  //初土俵の場所がその場所なら結果を表示 
                ?>
                                <td><?= $rikishi['result'] ?></td>

                            <?php
                                $rank[] = $rikishi['rank'];  //そのときの位を保存しておく
                                $flag = 0;
                                break;
                            } else {
                            ?>
                                <td></td> <!-- 初土俵の場所がその場所でないなら空欄とする-->
                        <?php
                                $cnt++;  //空欄の数をカウント
                            }
                        }
                    } else { ?>
                        <td><?= $rikishi['result'] ?></td>
                <?php
                        $rank[] = $rikishi['rank'];  //そのときの位を保存しておく
                        $flag = 0;
                    }

                    if ($rikishi['month'] == '11月場所') {  //1年の最後の場所のとき
                        echo '</tr>';

                        //位を表示する行
                        echo '<tr class=' . $class . '>';
                        echo "<td class=$class2>(" . $rikishi['wareki'] . ')</td>';  //最初に和暦を表示
                        echo "<td class=$class2>位</td>";
                        for ($i = 0; $i < $cnt; $i++) {  //$cntの数だけ空欄とする
                            echo '<td></td>';
                        }
                        foreach ($rank as $v => $x) {  //これまで保存していた位を表示していく
                            echo '<td>' . $x . '</td>';
                        }
                        echo '</tr>';
                        $rank = array();  //保存していた位を破棄する
                        $cnt = 0;
                        $flag = 1;
                        if ($class == 'background2') {  //次の行から背景色を変える
                            $class = 'background3';
                        } elseif ($class == 'background3') {
                            $class = 'background2';
                        }
                    }
                }
                ?>
            </tbody>

        </table>
        <a href="./index.php">力士一覧へ</a>
        <br>
        <br>
        <a href="../login/logout.php" class="btnBlue">ログアウト</a>
    </div>

</body>

</html>