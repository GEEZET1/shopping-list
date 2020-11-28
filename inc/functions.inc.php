<?php
session_start();
function check_if_field_is_empty($field) {
    foreach ($field as $key => $value) {
        if (empty($value)) {
            return false;

            exit();
        } else {
            return true;
        };
    };
};

function validate_email($email) {
    $regex = '/^(([^()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

    if (preg_match($regex, $email) > 0) {
        return true;
    } else {
        return false;
    }
};

function check_if_email_is_taken($email) {
    require 'config.inc.php';

    $sql = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) === 0) {
        return true;
    } else {
        return false;
    };
};

function validate_password($password) {
    $regex = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,}$/';

    if (preg_match($regex, $password) > 0) {
        return true;
    } else {
        return false;
    };
};

function check_if_passwords_match($password, $repeated) {
    if ($password !== $repeated) {
        return false;
    } else {
        return true;
    };
};

function create_user($credentials) {
    require 'config.inc.php';
    
    $login = explode('@', $credentials['email-address'])[0];
    $password = password_hash($credentials['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (login, email, password) VALUES ('$login', '{$credentials['email-address']}', '$password')";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    };
};

function redirect_to_page($page, $param) {
    if (empty($param)) {
        header("Location: ../{$page}");
        exit();
    } else {
        header("Location: ../{$page}?{$param}");
        exit();
    };
};

function check_login_credentials($credentials) {
    require 'config.inc.php';

    $sql = "SELECT password FROM users WHERE email = '{$_POST['email-address']}'";

    $result = mysqli_query($conn, $sql);
    $hash = mysqli_fetch_assoc($result);
    
    return password_verify($_POST['password'], $hash['password']);
};

function login_user($credentials) {
    require 'config.inc.php';

    $sql = "SELECT login, email, user_role FROM users WHERE email = '{$_POST['email-address']}'";

    $result = mysqli_query($conn, $sql);

    if ($data = mysqli_fetch_assoc($result)) {
        session_start();

        $_SESSION['user_role'] = $data['user_role'];
        $_SESSION['login'] = $data['login'];
        $_SESSION['email_address'] = $data['email'];

        return true;
    } else {
        return false;
    };
};

function add_article($article, $category, $unit) {
    require 'config.inc.php';

    $sql = "INSERT INTO articles (name, id_category, id_unit) VALUES ('$article', '$category', '$unit')";

    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    };
};

function delete_article($article) {
    require 'config.inc.php';

    $sql = "DELETE FROM articles WHERE id_article = $article";

    mysqli_query($conn, $sql);
};

function display_articles() {
    require 'config.inc.php';
   
    $result = mysqli_query($conn, "SELECT id_article, name FROM articles");

    echo '<select name="article">';
    while($data=mysqli_fetch_row($result)) {
        echo "<option value='$data[0]'>$data[1]</option>";
    };
    echo '</select>';
};

function display_articles_select($categoryId) {
    require 'config.inc.php';
   
    $result = mysqli_query($conn, "SELECT id_article, name FROM articles WHERE id_category = $categoryId");

    echo '<select name="article">';
    while($data=mysqli_fetch_row($result)) {
        echo "<option value='$data[0]' onClick='showUnits(this)'>$data[1]</option>";
    };
    echo '</select>';
    echo '<i class="fas fa-chevron-down"></i>';
}

function display_articles_category() {
    require 'config.inc.php';
    
    $result = mysqli_query($conn, "SELECT id_category, name FROM categories");

    echo '<select name="category">';
    while($data=mysqli_fetch_row($result)) {
        echo "<option value='$data[0]'>$data[1]</option>";
    };
    echo '</select>';
};

function display_articles_category_select() {
    require 'config.inc.php';
    
    $result = mysqli_query($conn, "SELECT id_category, name FROM categories");

    echo '<select name="category">';
    while($data=mysqli_fetch_row($result)) {
        echo "<option value='$data[0]' onClick='showArticles(this)'>$data[1]</option>";
    };
    echo '</select>';
};

function display_articles_unit() {
    require 'config.inc.php';
    
    $result = mysqli_query($conn, "SELECT id_unit, name FROM units");

    echo '<select name="unit">';
    while($data=mysqli_fetch_row($result)) {
        echo "<option value='$data[0]'>$data[1]</option>";
    };
    echo '</select>';
};

function display_units_select($articleId) {
    require 'config.inc.php';
    
    $result = mysqli_query($conn, "CALL  get_unit_for_article($articleId)");

    if ($data=mysqli_fetch_row($result)) {
        echo "<p name='$data[0]'>$data[1]</p>";
    };
}

function add_list($list) {
    require 'config.inc.php';
    // session_start();
    
    $listName = $_SESSION['email_address'].'_'.$list;

    $sql = "INSERT INTO lists(name) VALUES('$listName')";

    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    };
};

function add_lits_owners($list) {
    require 'config.inc.php';

    $owners = array();

    // wlasciciele
    foreach ($list as $owner => $value) {
        if (preg_match('/^owner[\d]$/', $owner) > 0) {
            $result = mysqli_query($conn, "SELECT id_user FROM users WHERE email = '$value'");

            $data = mysqli_fetch_assoc($result);
            
            if (!empty ($data)) {
                if (check_if_field_is_empty($data)) {
                    array_push($owners, $data['id_user']);
                } else {
                    display_warning_message('Looks like you didn\'t specify any list owner.');
                }
            } else {
                display_warning_message('Looks like email you specified as a list owner, doesn\'t have an account in our service yet.');
            };
        };
    };

    // id listy
    $listName = $_SESSION['email_address'].'_'.$list['listName'];
    $result = mysqli_query($conn, "SELECT id_list FROM lists WHERE name = '$listName'");
    $data = mysqli_fetch_assoc($result);
    $listId = $data['id_list'];

    // dodanie wlascicieli do listy
    for ($owner=0; $owner < count($owners); $owner++) { 
        // echo "INSERT INTO list_owner(id_list, id_owner) VALUES($listId, $owners[$owner])"; // ok

        mysqli_query($conn, "INSERT INTO list_owner(id_list, id_owner) VALUES($listId, $owners[$owner])");
    };
};

function display_warning_message($message) {
    echo '<div class="warning-message-div><p class="warning-message-paragraph">'.$message.'</p></div>';
}

function display_user_lists($email) {
    require 'config.inc.php';
    
    // id uzytkownika   
    $result = mysqli_query($conn, "SELECT id_user FROM users WHERE email = '$email'");
    $data = mysqli_fetch_assoc($result);
    $userId = $data['id_user'];

    // listy uzytkownika 
    $userLists = array();
    $result = mysqli_query($conn, "SELECT id_list FROM list_owner WHERE id_owner = $userId");
    while ($data = mysqli_fetch_assoc($result)) {
        array_push($userLists, $data['id_list']);
    };
    
    foreach ($userLists as $key => $listId) {
        display_lists($listId, $email);
    };
};

function display_list_owners($list) {
    require 'config.inc.php';

    $sql = "CALL get_list_owners($list)";
    $result = mysqli_query($conn, $sql);

    echo '<p>';
    while ($data = mysqli_fetch_array($result)) {
        echo $data[1].'<br>';
    }
    echo '</p>';
}

function display_lists($listId, $email) {
    require 'config.inc.php';
    
    $sql = "CALL list_details($listId)";
    $result = mysqli_query($conn, $sql);

    display_list_name($listId, $email, 1);
};

function display_list_name($listId, $email, $option) {
    // zmienna $option określa czy nagłówek pierwszego stopnia ma być odnośnikiem do listy(1), czy zwykłą nazwą(0)
    require 'config.inc.php';

    $sql = "SELECT name, create_date, last_edit FROM lists WHERE id_list = '$listId'";
    $result = mysqli_query($conn, $sql);

    echo '<div class="list-name">';
    if ($option == 1) {
        if ($name = mysqli_fetch_array($result)) {
            echo "<h1 onClick='showList(this)' id={$listId}>".str_replace($email.'_', '', $name[0]).'<i class="far fa-hand-pointer"></i></h1><i class="far fa-trash-alt fa-lg" onClick="deleteList('.$listId.',\''.$name[0].'\')"></i>';
        }
    } else if ($option == 0) {
        if ($name = mysqli_fetch_array($result)) {
            echo '<p>Created: '.$name[1].'<br>Last edited: '.$name[2].'</p>'; // data utworzenia listy i data ostatniej aktualizacji
            echo "<h1 id={$listId}>".strtoupper($email).'</h1>'; // zmienna $email przyjęła wartość nazwy listy poprzez przekazanie jej w JS w funkcji showList(list)
            display_list_owners($listId);
        };
    };
    echo '</div>';
};

function display_list_articles($listId) {
    require 'config.inc.php';

    $sql = "CALL list_details($listId)";
    $result = mysqli_query($conn, $sql);
    
    echo '<div class="list-articles">';
    while($data = mysqli_fetch_array($result)) {
        if($data[5] == 'false') {
            display_list_article($data, $listId);
        } else {
            display_list_article_bought($data, $listId);
        }
    };
   
    echo '</div><div class="add-article-to-list" name="'.$listId.'" onClick="showModal(\'addArticleToList\')"><i class="fas fa-plus-square"></i></div>';
};

function display_list_article($data, $listId) {
    echo '<div class="list-article"><p id="'.$data[2].'">'.$data[1].'('.$data[7].')<span class="list-article-category">/'.$data[3].'</span></p> <p><input type="number" step="0.01" min="0" placeholder="[price]" class="list-article-price">/'.$data[4].'<i class="fas fa-shopping-basket" onClick="changePrice('.$data[2].','.$listId.',this); updateTotalValue('.$listId.')"></i><i class="fas fa-times" onClick="deleteArticleFromList('.$data[2].','.$listId.',this); updateTotalValue('.$listId.')"></i></p></div>';
};

function display_list_article_bought($data, $listId) {
    echo '<div class="list-article article-bought"><p id="'.$data[2].'">'.$data[1].'('.$data[7].')'.'<span class="list-article-category">/'.$data[3].'</span></p> <p><input type="number" step="0.01" min="0" placeholder="'.$data[6].'" class="list-article-price">/'.$data[4].'<i class="fas fa-edit" onClick="changePrice('.$data[2].','.$listId.',this); updateTotalValue('.$listId.')"></i><i class="fas fa-times" onClick="deleteArticleFromList('.$data[2].','.$listId.',this); updateTotalValue('.$listId.')"></i></p></div>';
};

function display_list_value($list) {
    require 'config.inc.php';

    $sql = "CALL get_list_value($list)";
    $result = mysqli_query($conn, $sql);
    
    if ($data = mysqli_fetch_array($result)) {
        echo '<p>Total value: '.$data[1].'</p>';
    };
};

function display_list_detail($list) {
    echo '<div class="list-total-value">';
        display_list_value($list['listId']);
    echo '</div>';

    display_list_name($list['listId'], $list['listName'], 0);
    display_list_articles($list['listId']);
};

function update_article($article) {
    require 'config.inc.php';

    $listId = $article['listId'];
    $articleId = $article['articleId'];
    $articlePrice = $article['articlePrice'];

    $sql = "CALL update_article($listId, $articleId, $articlePrice)";
    mysqli_query($conn, $sql);
};

function delete_article_from_list($list) {
    require 'config.inc.php';

    $listId = $list['listId'];
    $articleId = $list['articleId'];

    $sql = "CALL delete_article($listId, $articleId)";;
    mysqli_query($conn, $sql);
};

function add_article_to_list($list) {
    require 'config.inc.php';

    $listId = $list['listId'];
    $articleId = $list['articleId'];

    $sql = "CALL  add_article_to_list($listId, $articleId)";
    mysqli_query($conn, $sql);
};

function delete_list($listId) {
    require 'config.inc.php';

    $sql = "CALL delete_list($listId)";
    mysqli_query($conn, $sql);
};

function check_list_name($listName) {
    require 'config.inc.php';
    
    $listName = $_SESSION['email_address'].'_'.$listName;
    $sql = "SELECT check_list_name('$listName')";
    $result = mysqli_query($conn, $sql);
    
    if($data = mysqli_fetch_row($result)) {
        if ($data[0] == '0') {
            return true;
        } elseif ($data[0] == '1') {
            return false;
        }
    }
};

function error_handler($error) {
    switch ($error) {
        case 'success':
            display_warning_message('Action performed successfully.');
            break;

        case 'error':
            display_warning_message('Action failed. Try again');
            break;

        case 'passwordsDoesntMatch':
            display_warning_message('Passwords does not match. Try again.');
            break;
        
        case 'wrongPassword':
            display_warning_message('Password is too weak. Try again.');
            break;
            
        case 'emailTaken':
            display_warning_message('Email is already taken. Try again.');
            break;

        case 'wrongEmail':
            display_warning_message('Wrong email. Try again.');
            break;

        case 'emptyField':
            display_warning_message('Do not leave empty fields. Try again.');
            break;

        case 'wrongCredentials':
             display_warning_message('Login and/or password is wrong. Try again.');
            break;
        
        case 'emailNotFound':
            display_warning_message('Email was not found. Try again.');
             break;

        default:
            break;
    };
};