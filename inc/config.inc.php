<?php
    $conn = mysqli_connect('localhost', 'root', '', 'shopping_list') or die (header('Location: ../index.php?connectionError'));

    mysqli_set_charset($conn, "utf8");
