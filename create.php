<?php
    session_start();
    $polls = json_decode(file_get_contents("polls.json"), true);
    $users = json_decode(file_get_contents("users.json"), true);
    $groups = json_decode(file_get_contents("groups.json"), true);
    $errors = "";

    $groupnames = array_column($groups, "name");

    if(!isset($_SESSION["loggedin"]) || !$users[$_SESSION["userid"]]["isAdmin"]) {
        header("location: index.php?alert=3");
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $question = $_POST["question"] ?? "";
        trim($question);
        $options = preg_split("/\r\n|[\r\n]/", $_POST['options']);
        $isMultiple = $_POST["isMultiple"] === "igen" ? true : false;
        $createdAt = $_POST["createdAt"];
        $deadline = $_POST["deadline"] ?? "";
        $id = "poll" . count($polls) + 1;
        $group = $_POST["groups"];

        if($question === "" || count($options) < 2 || $deadline === "") {
            $errors = "Kérjük, adj meg minden adatot!";
        }
        else {
            $answers = [];
            foreach($options as $o) {
                $answers[$o] = 0;
            }

            $newPoll = [
                "id" => $id,
                "question" => $question,
                "options" => $options,
                "isMultiple" => $isMultiple,
                "createdAt" => $createdAt,
                "deadline" => $deadline,
                "group" => $group,
                "answers" => $answers,
                "voted" => []
            ];

            $polls[$id] = $newPoll;
            file_put_contents("polls.json", json_encode($polls, JSON_PRETTY_PRINT));
            header("location: index.php?alert=1");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Szavazás létrehozása</title>
</head>
<body>
    <div class="navbar">
        <a href="/index.php">Főoldal</a>
        <?= isset($_SESSION["loggedin"]) && $_SESSION["username"] == "admin" ? "<a href='/groups.php'>Csoportok</a>" : ""?>
        <div class="align-right" <?=isset($_SESSION["loggedin"]) ? "hidden" : ""?>>
            <a href="login.php">Bejelentkezés</a>
            <a href="register.php">Regisztráció</a>
        </div>
        <div class="align-right" <?=isset($_SESSION["loggedin"]) ? "" : "hidden"?>>
            <span><?=$_SESSION["username"]?></span>
            <a href="index.php?logout=1">Kijelentkezés</a>
        </div>
    </div>
    <div class="main-new-poll">
        <p class="sign">Szavazás létrehozása</p>
        <form class="form" method="post" novalidate>
            <input type="text" name="question" placeholder="A szavazás címe"><br>
            <textarea name="options" id="" cols="30" rows="5" placeholder="Válaszlehetőségek"></textarea>
            <div class="align-left">
                <label for="isMultiple">Lehetséges több szavazat leadása?</label>
            </div>
            <div class="options-container">
                <label class="option-item" for="igen">
                    <input type="radio" name="isMultiple" id="igen" value="igen">
                    <span>Igen</span>
                </label>
                <label class="option-item" for="nem">
                    <input type="radio" name="isMultiple" id="nem" value="nem" checked>
                    <span>Nem</span>
                </label>
            </div><br>
            <div class="align-left">
                <label for="groups">Ki láthatja a szavazást?</label>
            </div>
            <select name="groups" id="groups">
                <?php foreach($groupnames as $gn):?>
                    <option value="<?=$gn?>"><?=$gn?></option>
                <?php endforeach ?>
                <option value="everyone" selected>Minden felhasználó</option>
            </select>
            <div class="align-left">
                <label for="deadline">Leadás határideje:</label>
            </div>
            <input type="date" name="deadline" min="<?php echo date("Y-m-d")?>">
            <div class="align-left">
                <label for="createdAt">Létrehozás ideje:</label>
            </div>
            <input type="date" name="createdAt" value=<?=date("Y-m-d")?> readonly>
            <button>Szavazás létrehozása</button>
        </form>   
        <div class="error" <?=$errors === "" ? "hidden" : ""?>><?=$errors?></div>    
    </div>
</body>
</html>