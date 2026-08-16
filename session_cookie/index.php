<?php

session_start();


$session_timeout = 1800;


if (isset($_SESSION["username"]) && isset($_SESSION["last_activity"])) {

    if (time() - $_SESSION["last_activity"] > $session_timeout) {

        session_unset();
        session_destroy();

        session_start();
        $_SESSION["timeout_message"] = "Your session expired. Please login again.";
    }
}


if (isset($_SESSION["username"])) {
    $_SESSION["last_activity"] = time();
}

if (isset($_POST["login"])) {

    $username = trim($_POST["username"]);

    
    if ($username === "") {
        $error = "Please enter your username.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } else {

        
        session_regenerate_id(true);

      
        $_SESSION["username"] = $username;

       
        $_SESSION["last_activity"] = time();

        
        if (isset($_POST["remember"])) {

            setcookie(
                "remember_user",
                $username,
                [
                    "expires" => time() + (86400 * 30),
                    "path" => "/",
                    "httponly" => true,
                    "samesite" => "Lax"
                ]
            );

        } else {

           
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
        }

        header("Location: dashboard.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Session & Cookie</title>

    <link rel="stylesheet" href="s.css">

</head>

<body>

<div class="container">

    <div class="icon">🔐</div>

    <h2>Welcome Back</h2>

    <p class="subtitle">
        Login to continue
    </p>

    <?php

    if (isset($_SESSION["timeout_message"])) {

        echo '<div class="cookie-error">';
        echo htmlspecialchars($_SESSION["timeout_message"]);
        echo '</div>';

        unset($_SESSION["timeout_message"]);
    }

    if (isset($error)) {

        echo '<div class="cookie-error">';
        echo htmlspecialchars($error);
        echo '</div>';
    }

    ?>

    <form method="POST">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter your username"
            minlength="3"
            required
        >

        <label class="remember">

            <input
                type="checkbox"
                name="remember"
            >

            Remember Me

        </label>

        <button type="submit" name="login">
            Login
        </button>

    </form>

</div>

</body>
</html>