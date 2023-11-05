<?php
    session_start();
    $polls = json_decode(file_get_contents("polls.json"), true);
    $users = json_decode(file_get_contents("users.json"), true);
    $groups = json_decode(file_get_contents("groups.json"), true);

    if(isset($_GET["delete"])) {
        $id = $_GET["delete"];
        unset($polls[$id]);
        file_put_contents("polls.json", json_encode($polls, JSON_PRETTY_PRINT));
    }

    if(isset($_GET["logout"])) {
        unset($_SESSION["loggedin"]);
        unset($_SESSION["userid"]);
        unset($_SESSION["username"]);
    }

    $dl = array_column($polls, 'createdAt');
    array_multisort($dl, SORT_DESC, $polls);
    
    $current = date("Y-m-d");
    $active = array_filter($polls, fn($poll) => date("Y-m-d", strtotime($poll["deadline"])) >= $current);
    $expired = array_filter($polls, fn($poll) => date("Y-m-d", strtotime($poll["deadline"])) < $current);

    function getMessage() {
        $message = "";
        if(isset($_GET["alert"])) {
            switch ($_GET["alert"]) {
                case 0:
                    $message = "Sikeresen leadtad a szavazatod!";
                    break;
                case 1:
                    $message = "Sikeresen létrehoztad a szavazást!";
                    break;
                case 2:
                    $message = "Sikeres bejelentkezés!";
                    break;
                case 3:
                    $message = "Szavazást csak az admin felhasználó hozhat létre!";
                    break;
                case 4:
                    $message = "Sikeresen létrehoztad a csoportot!";
            }
        }
        return $message;
    }

    function getClass() {
        $class = "";
        if(isset($_GET["alert"])) {
            if($_GET["alert"] == 3) {
                $class = "unsuccess";
            }
            else $class = "success";
        }
        return $class;
    }

    function hasVoted($id) {
        global $polls;
        $voted = false;
        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
            if(array_key_exists($_SESSION["userid"], $polls[$id]["voted"])) {
                $voted = true;
            }
        }
        return $voted;
    }

    function isAdmin() {
        global $users;
        return (isset($_SESSION["loggedin"]) && $users[$_SESSION["userid"]]["isAdmin"]);
    }

    function inGroup($group) {
        global $groups;
        $username = isset($_SESSION["loggedin"]) ? $_SESSION["username"] : "guest";

        if($group === "everyone" || $username === "admin"
            || in_array($username, $groups[$group]["users"])) {
            return true;
        }
        return false;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Főoldal</title>
</head>
<body>
    <div id="alert"  <?=!isset($_GET["alert"]) ? "hidden" : ""?> class=<?=getClass()?>>
        <span id="closebtn">&times;</span>
        <?=getMessage()?>
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
    <div class="index-container">
        <img src="index.jpg" alt=""/>
        <div class="centered">
            <h1 class="title">Hozd létre saját szavazásod!</h1>  
            <div class="review">Készítsd el saját szavazólapodat, vagy add le szavazatod az alábbi szavazólapokra!</div>
        </div>
    </div><br>
    <div class="button-center">
        <a href="create.php"><button class="red-button">Szavazás létrehozása</button></a>
    </div>
    <div class="polls">
        <h2>Aktív szavazólapok:</h2>
        <div class="grid-container">
            <?php foreach($active as $a): ?>
                <?php if(inGroup($a["group"])): ?>
                    <div class="grid-item active">
                        <h3><?=$a["question"]?></h3>
                        <div class="align-bottom">
                            <p>#<?=substr($a["id"], 4, 2)?></p>
                            <p>Létrehozva: <?=$a["createdAt"]?></p>
                            <p>Elérhető eddig: <?=$a["deadline"]?></p>
                            <div class="button-center">
                                <a href=<?=isset($_SESSION["loggedin"]) ? 'vote.php?id=' . $a["id"] : "login.php"?>>
                                    <button><?=hasVoted($a["id"]) ? "Szavazat frissítése" : "Erre szavazok!"?></button>
                                </a>
                            </div>
                            <div class="button-center" <?=isAdmin() ? "" : "hidden"?>>
                                <a href="index.php?delete=<?=$a["id"]?>"><button id='delete'>Szavazólap törlése</button></a>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
        <h2>Lejárt szavazólapok:</h2>
        <div class="grid-container">
            <?php foreach($expired as $e): ?>
                <?php if(inGroup($e["group"])): ?>
                    <div class="grid-item expired">
                        <h3><?=$e["question"]?></h3>
                        <div class="align-bottom">
                            <p>#<?=substr($e["id"], 4, 2)?></p>
                            <p>Létrehozva: <?=$e["createdAt"]?></p>
                            <p>Elérhető eddig: <?=$e["deadline"]?></p>
                            <div class="button-center">
                                <a href="results.php?id=<?=$e["id"]?>"><button>Eredmények</button></a>
                            </div>
                            <div class="button-center" <?=isAdmin() ? "" : "hidden"?>>
                                <a href="index.php?delete=<?=$a["id"]?>"><button id='delete'>Szavazólap törlése</button></a>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
</body>
<script src="script.js"></script>
</html>
