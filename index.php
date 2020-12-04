<?php 
require_once 'inc/functions.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600&display=swap" rel="stylesheet"> 
    <script src="https://kit.fontawesome.com/3f479ffa7c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">

    <title>Shopping List</title>
</head>
<body>
    <nav>
        <?php if (isset($_SESSION['login']) && $_SESSION['user_role'] == 'administrator') { ?>
            <div id="manage-articles">
                <h1>Manage Articles</h1>
            </div>

            <div id="create-list">
                <h1>Create List</h1>
            </div>

            <div id="my-lists">
                <h1>My Lists</h1>
            </div>

            <div id="logout" onClick="logout()">
                <h1>Logout</h1>
            </div>
        <?php } else if(isset($_SESSION['login'])) { ?>
            <div id="create-list">
                <h1>Create List</h1>
            </div>

            <div id="my-lists">
                <h1>My Lists</h1>
            </div>

            <div id="logout" onClick="logout()">
                <h1>Logout</h1>
            </div>
        <?php } else {?>
            <div id="create-account">
                <h1>Create Account</h1>
            </div>

            <div id="login">
                <h1>Login</h1>
            </div>
        <?php }?>
    </nav>

    <main>
        <?php if (isset($_GET)) {
            foreach ($_GET as $error => $value) {
                error_handler($error);
            };
        } ?>
        <div class="intro">
            <h1>Shopping</h1>
            <h2>It's simple.</h2>
            <hr>
            <p>Pick product, add to list, manage prices & quanity.</p>
        </div>
    </main>
    <script src="app.js"></script>
</body>
</html>