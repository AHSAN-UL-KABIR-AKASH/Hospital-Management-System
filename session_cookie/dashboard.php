<?php

session_start();


$session_timeout = 1800;


if (!isset($_SESSION["username"])) {

    header("Location: index.php");
    exit();
}


if (
    isset($_SESSION["last_activity"]) &&
    (time() - $_SESSION["last_activity"] > $session_timeout)
) {

   
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit();
}


$_SESSION["last_activity"] = time();

$username = $_SESSION["username"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link rel="stylesheet" href="s.css">

</head>

<body>

<div class="container dashboard">

    <div class="icon">👋</div>

    <h2>Dashboard</h2>

    <div class="welcome-box">

        <p>Welcome,</p>

        <h3>
            <?php echo htmlspecialchars($username); ?>
        </h3>

    </div>

    <div class="info">

        <p>
            <strong>Session Status</strong>
        </p>

        <p>
            Your username is currently stored in a
            <strong>PHP Session</strong>.
        </p>

        <p>
            Session timeout:
            <strong>30 minutes</strong>
        </p>

    </div>

    <div class="buttons">

        <a href="cookie.php" class="button">
            🍪 View Cookie
        </a>

        <a href="logout.php" class="button logout-button">
            🚪 Logout
        </a>

    </div>

</div>

</body>
</html>