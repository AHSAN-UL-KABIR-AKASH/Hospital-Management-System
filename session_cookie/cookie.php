<?php


if (isset($_COOKIE["remember_user"])) {

    $username = $_COOKIE["remember_user"];

    $cookie_found = true;

} else {

    $username = "No cookie found";

    $cookie_found = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cookie Information</title>

    <link rel="stylesheet" href="s.css">

</head>

<body>

<div class="container">

    <div class="icon">🍪</div>

    <h2>Cookie Information</h2>

    <?php if ($cookie_found): ?>

        <div class="cookie-success">

            <p>
                Remembered Username
            </p>

            <h3>
                <?php echo htmlspecialchars($username); ?>
            </h3>

        </div>

        <div class="info">

            <p>
                This username was retrieved from the
                <strong>browser cookie</strong>.
            </p>

            <p>
                Cookie expiration:
                <strong>30 days</strong>
            </p>

        </div>

    <?php else: ?>

        <div class="cookie-error">

            <h3>
                No Cookie Found
            </h3>

            <p>
                The Remember Me option was not selected during login.
            </p>

        </div>

    <?php endif; ?>

    <div class="buttons">

        <a href="dashboard.php" class="button">
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>
</html>