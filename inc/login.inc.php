<?php
    if (isset($_POST['login-button'])) {
        require 'config.inc.php';
        require_once 'functions.inc.php';

        if (check_if_field_is_empty($_POST)) {
            if (!check_if_email_is_taken($_POST['email-address'])) {
                if (check_login_credentials($_POST)) {
                    if (login_user($_POST)) {
                        redirect_to_page('index.php', 'success');
                    } else {
                        redirect_to_page('index.php', 'error');
                    }
                } else {
                    redirect_to_page('index.php', 'wrongCredentials');
                }
            } else {
                redirect_to_page('index.php', 'emailNotFound');
            }
        } else {
            redirect_to_page('index.php', 'emptyField');
        }
    } else {
        redirect_to_page('index.php', null);
    };
