<?php
    session_start();
    $polls = json_decode(file_get_contents("polls.json"), true);
    $users = json_decode(file_get_contents("users.json"), true);
    $groups = json_decode(file_get_contents("groups.json"), true);
    $errors = "";

    $usernames = array_column($users, "username");
    unset($usernames[0]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $groupname = $_POST["groupname"] ?? "";
        trim($groupname);
        $users = $_POST["users"] ?? "";

        if($groupname === "" || $users === "") {
            $errors = "Kérjük, adj meg minden adatot!";
        }
        else {
            $group = [
                "name" => $groupname,
                "users" => $users
            ];
            $groups[$groupname] = $group;
            file_put_contents("groups.json", json_encode($groups, JSON_PRETTY_PRINT));
            header("location: index.php?alert=4");
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
    <title>Csoportok</title>
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
    <div class="main">
        <p class="sign">Csoport létrehozása</p>
            <form method="post" novalidate>
                <input name="groupname" type="text" placeholder="Csoport neve">
                <select name="users[]" id="users" multiple>
                    <?php foreach($usernames as $un): ?>
                        <option name="<?=$un?>" value="<?=$un?>"><?=$un?></option>
                    <?php endforeach ?>
                </select>
                <button>Csoport létrehozása</button>
            </form>
            <div class="error" <?=$errors === "" ? "hidden" : ""?>><?=$errors?></div>   
    </div>
</body>
<script src="script.js"></script>
</html>
