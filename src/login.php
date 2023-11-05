<?php
    session_start();
    $users = json_decode(file_get_contents("users.json"), true);
    $errors = "";

    $username = $_POST["username"] ?? "";
    trim($username);
    $password = $_POST["password"] ?? "";
    trim($password);

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if($username !== "" && $password !== "") {
            $userid = "";
            foreach($users as $u) {
                if($u["username"] === $username) {
                    $userid = $u["id"];
                }
            }
            if($userid === "" || $users[$userid]["password"] !== $password) {
                $errors = "Hibás felhasználónév vagy jelszó!";
            }
            else {
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["userid"] = $userid;
                header("location: index.php?alert=2");
            }
        }
        else {
            header("location: login.php?alert=1");
        }
    }

    function getMessage() {
        $message = "";
        if(isset($_GET["alert"])) {
            switch ($_GET["alert"]) {
                case 0:
                    $message = "Sikeres regisztráció!";
                    break;
                case 1:
                    $message = "Kérjük, adj meg minden adatot!";
                    break;
            }
        }
        return $message;
    }

    function getClass() {
        $class = "";
        if(isset($_GET["alert"])) {
            switch ($_GET["alert"]) {
                case 0:
                    $class = "success";
                    break;
                case 1:
                    $class = "unsuccess";
                    break;
            }
        }
        return $class;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Bejelentkezés</title>
</head>
<body>
    <div id="alert" <?=!isset($_GET["alert"]) ? "hidden" : ""?> class=<?=getClass()?> >
        <span id="closebtn">&times;</span>
        <?=getMessage()?>
    </div>
    <div class="navbar">
        <a href="index.php">Főoldal</a>
        <div class="align-right">
            <a href="login.php">Bejelentkezés</a>
            <a href="register.php">Regisztráció</a>
        </div>
    </div>
    <div class="main">
        <p class="sign">Bejelentkezés</p>
            <form method="post" novalidate>
                <input name="username" type="text" placeholder="Felhasználónév">
                <input name="password" type="password" placeholder="Jelszó">
                <button>Belépés</button>
            </form>
            <div class="error" <?=$errors === "" ? "hidden" : ""?>><?=$errors?></div>
        <div class="register">Még nem vagy tag? <a href="register.php">Regisztrálj!</div>       
    </div>
</body>
<script src="script.js"></script>
</html>
