<?php
    session_start();
    $noanswer = false;
    $id = $_GET["id"];
    $polls = json_decode(file_get_contents("polls.json"), true);

    $poll = $polls[$id];

    $now = date("Y-m-d");
    $deadline = $poll["deadline"];
    if($now > $deadline) {
        header("location: index.php");
    }

    $userid = $_SESSION["userid"];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(count($_POST) >= 1) {
            if(is_string($_POST[$id])) {
                if(array_key_exists($_SESSION["userid"], $polls[$id]["voted"])) {
                    $prev_vote = $polls[$id]["voted"][$_SESSION["userid"]];
                    $polls[$id]["answers"][$prev_vote]--;
                    $vote = $_POST[$id];
                    $polls[$id]["answers"][$vote]++;
                }
                else {
                    $vote = $_POST[$id];
                    $polls[$id]["answers"][$vote]++;
                }
            }
            else {
                if(array_key_exists($_SESSION["userid"], $polls[$id]["voted"])) {
                    foreach($polls[$id]["voted"][$_SESSION["userid"]] as $prev_vote) {
                        $polls[$id]["answers"][$prev_vote]--;
                    }
                    foreach($_POST[$id] as $vote) {
                        $polls[$id]["answers"][$vote]++;
                    }
                }
                else {
                    foreach($_POST[$id] as $vote) {
                        $count = intval($polls[$id]["answers"][$vote]) + 1;
                        $polls[$id]["answers"][$vote] = $count;
                    }
                }
            }
            $polls[$id]["voted"][$_SESSION["userid"]] = $_POST[$id];
            file_put_contents("polls.json", json_encode($polls, JSON_PRETTY_PRINT));
            header("location: index.php?alert=0");
        }
        else {
            $noanswer = true;
        }
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Szavazás</title>
</head>
<body>
    <div id="alert" class="unsuccess" <?=!$noanswer ? "hidden" : ""?>>
        <span id="closebtn">&times;</span>
        <?= $noanswer ? "Kérjük, jelölj meg egy választ!" : ""?>
    </div>
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
    <form method="post" action="">
        <div class="details">
            <h1><?=$poll["question"]?></h1>
            <div class="options-container">
                <?php foreach($poll["options"] as $o): ?>
                <label class="option-item" for="<?=$o?>">
                    <input type=<?=$poll["isMultiple"] ? "checkbox" : "radio"?> 
                    name="<?=$poll["isMultiple"] ? $id . '[]' : $id?>" id="<?=$o?>" value="<?=$o?>">
                    <?=ucfirst($o)?>
                </label>
                <?php endforeach ?>
            </div>
            <p>Létrehozva: <?=$poll["createdAt"]?></p>
            <p>Elérhető eddig: <?=$poll["deadline"]?></p>
            <div class="button-center">
                <button class="red-button">Szavazat leadása</button>
            </div>
        </div>
    </form>
</body>
<script src="script.js"></script>
</html>