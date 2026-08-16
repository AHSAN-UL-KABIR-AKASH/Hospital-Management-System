<?php

session_start();


$_SESSION = [];


if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}


session_destroy();


if (isset($_COOKIE["remember_user"])) {

    setcookie(
        "remember_user",
        "",
        [
            "expires" => time() - 3600,
            "path" => "/",
            "httponly" => true,
            "samesite" => "Lax"
        ]
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Logout</title>

    <link rel="stylesheet" href="s.css">

</head>

<body>

<div class="container">

    <div class="icon">✅</div>

    <h2>
        Logout Successful
    </h2>

    <div class="logout-message">

        <p>
            Your PHP session has been successfully destroyed.
        </p>

        <p>
            The Remember Me cookie has also been removed.
        </p>

    </div>

    <div class="buttons">

        <a href="index.php" class="button">
            ← Go to Login
        </a>

    </div>

</div>

</body>
</html>