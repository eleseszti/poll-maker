<?php
    $users = json_decode(file_get_contents("users.json"), true);

    $username = $_POST["username"] ?? "";
    trim($username);
    $email = $_POST["email"] ?? "";
    trim($email);
    $password = $_POST["password"] ?? "";
    trim($password);
    $repassword = $_POST["repassword"] ?? "";
    trim($repassword);

    $errors = [];

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Érvénytelen e-mail cím!";
        }
        if($password !== $repassword) {
            $errors["repassword"] = "Nem egyeznek a jelszavak!";
        }
        if($username === "") {
            $errors["username"] = "Kérjük, adj meg egy felhasználónevet!";
        }
        if($email === "") {
            $errors["email"] = "Kérjük, add meg az email-címed!";
        }
        if($password === "") {
            $errors["password"] = "Kérjük, adj meg egy jelszót!";
        }
        if($repassword === "") {
            $errors["repassword"] = "Kérjük, add meg újra a jelszavad!";
        }
        else if(count($errors) === 0) {
            $count = count($users) + 1;
            $userid = "userid" . $count;
            $newUser = [
                "id" => $userid,
                "username" => $username,
                "email" => $email,
                "password" => $password,
                "isAdmin" => false
            ];
            $users[$userid] = $newUser;
            file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));
            header("location: login.php?alert=0");
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
    <title>Regisztráció</title>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Főoldal</a>
        <div class="align-right">
            <a href="login.php">Bejelentkezés</a>
            <a href="register.php">Regisztráció</a>
        </div>
    </div>
    <div class="main">
        <p class="sign">Regisztráció</p>
        <form method="post" novalidate>
            <input class="<?=isset($errors["username"]) ? "error-input" : ""?>" type="text" name="username" placeholder="Felhasználónév" value=<?=$username?>>
            <div class="error" <?=!isset($errors["username"]) ? "hidden" : ""?>>
                <?=$errors["username"] ?? ""?>
            </div>
            <input class="<?=isset($errors["email"]) ? "error-input" : ""?>" type="text" name="email" placeholder="E-mail cím" value=<?=$email?>>
            <div class="error" <?=!isset($errors["email"]) ? "hidden" : ""?>>
                <?=$errors["email"] ?? ""?>
            </div>
            <input class="<?=isset($errors["password"]) ? "error-input" : ""?>" type="password" name="password" placeholder="Jelszó" value=<?=$password?>>
            <div class="error" <?=!isset($errors["password"]) ? "hidden" : ""?>>
                <?=$errors["password"] ?? ""?>
            </div>
            <input class="<?=isset($errors["repassword"]) ? "error-input" : ""?>" type="password" name="repassword" placeholder="Jelszó újra" value=<?=$repassword?>>
            <div class="error" <?=!isset($errors["repassword"]) ? "hidden" : ""?>>
                <?=$errors["repassword"] ?? ""?>
            </div>
            <a href="login.php"><button>Regisztráció</button></a>
            <div class="register">Már tag vagy? <a href="login.php">Lépj be!</div>
        </form>      
    </div>
</body>
</html>