<?php
require_once 'functions.inc.php';

if (isset($_POST['create-account'])) {
    if (check_if_field_is_empty($_POST)) {
        if (validate_email($_POST['email-address'])) {
            if (check_if_email_is_taken($_POST['email-address'])) {
                if (validate_password($_POST['password'])) {
                    if (check_if_passwords_match($_POST['password'], $_POST['password-repeat'])) {
                        if (create_user($_POST)) {
                            redirect_to_page('index.php', 'success');
                        } else {
                            redirect_to_page('index.php', 'error');
                        }
                    } else {
                        redirect_to_page('index.php', 'passwordsDoesntMatch');
                    }
                } else {
                    redirect_to_page('index.php', 'wrongPassword');
                }
            } else {
                redirect_to_page('index.php', 'emailTaken');
            };
        } else {
            redirect_to_page('index.php', 'wrongEmail');
        };
    } else {
        redirect_to_page('index.php', 'emptyField');
    };
} else {
    redirect_to_page('index.php', null);
}