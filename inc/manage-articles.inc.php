<?php
require_once './functions.inc.php';
if (isset($_GET['article']) && isset($_GET['category']) && isset($_GET['unit']) && isset($_GET['add-article'])) {
    add_article($_GET['article'], $_GET['category'], $_GET['unit']);
} else if (isset($_GET['article']) && isset($_GET['delete-article'])){
    delete_article($_GET['article']);
} else {
    redirect_to_page('index.php', null);
}