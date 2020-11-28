<?php
require_once './functions.inc.php';
require 'config.inc.php';

if (isset($_GET['listName']) && isset($_GET['create-list'])) {
    if (check_if_field_is_empty($_GET)) {
        if (check_list_name($_GET['listName'])) {
            if (add_list($_GET['listName'])) {
                add_lits_owners($_GET);
                display_warning_message('List created successfully.');
            } else {
                display_warning_message('An error ocured. Try again.');
            };
        } else {
            display_warning_message('List already exists. Try other name.');
        };
    } else {
        return false;
    };
} else if (isset($_GET['listId']) && isset($_GET['show-list'])) {
    display_list_detail($_GET);
} else if (isset($_GET['listId']) && isset($_GET['articleId']) && isset($_GET['articlePrice']) && isset($_GET['change-price'])) {
    update_article($_GET);
} else if (isset($_GET['listId']) && isset($_GET['articleId']) && isset($_GET['delete-article-from-list'])) {
    delete_article_from_list($_GET);
} else if (isset($_GET['listId']) && isset($_GET['articleId']) && isset($_GET['add-article-to-list'])) {
    if (check_if_field_is_empty($_GET)) {
        add_article_to_list($_GET);
    } else {
        display_warning_message('An error ocured. Try again.');
    }
} else if (isset($_GET['listId']) && isset($_GET['update-total-value'])) {
    display_list_value($_GET['listId']);
} else if (isset($_GET['listId']) && isset($_GET['delete-list'])) {
    delete_list($_GET['listId']);
} else if (isset($_GET['listId']) && isset($_GET['emailAddress']) && isset($_GET['add-subowner'])) {
    add_subowner($_GET['listId'], $_GET['emailAddress']);
} else if (isset($_GET['categoryId'])) {
    display_articles_select($_GET['categoryId']);
} else if (isset($_GET['articleId'])) {
    display_units_select($_GET['articleId']);
} else {
    display_warning_message("An error ocured. Try again.");
};

