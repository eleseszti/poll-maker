<?php
    session_start();
    $polls = json_decode(file_get_contents("polls.json"), true);
    $id = $_GET["id"];
    $poll = $polls[$id];
    $answers = 0;

    $now = date("Y-m-d");
    $deadline = $poll["deadline"];
    if($now < $deadline) {
        header("location: index.php");
    }

    foreach($poll["options"] as $o) {
        $answers += $poll["answers"][$o];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Eredmények</title>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Főoldal</a>
        <?= isset($_SESSION["loggedin"]) && $_SESSION["username"] == "admin" ? "<a href='groups.php'>Csoportok</a>" : ""?>
        <div class="align-right" <?=isset($_SESSION["loggedin"]) ? "hidden" : ""?>>
            <a href="login.php">Bejelentkezés</a>
            <a href="register.php">Regisztráció</a>
        </div>
        <div class="align-right" <?=isset($_SESSION["loggedin"]) ? "" : "hidden"?>>
            <span><?=$_SESSION["username"]?></span>
            <a href="index.php?logout=1">Kijelentkezés</a>
        </div>
    </div>
    <div class="polls">
        <h2><?=$poll["question"]?></h2>
        <div class="options-container">
            <?php foreach($poll["options"] as $o): ?>
                <div class="option-item" style="background: linear-gradient(
                                                to right, 
                                                rgb(64, 130, 109) <?=($poll["answers"][$o] / $answers) * 100 ?>%, 
                                                rgb(192,192,192) <?=($poll["answers"][$o] / $answers) * 100 ?>%">
                    <span>
                        <h3><?=ucfirst($o)?></h3>
                        <span class="align-right">
                            <h3><?=$poll["answers"][$o]?> szavazat</h3>
                        </span>
                    </span>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</body>
</html>