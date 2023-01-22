<?php

//      ------ obecné funkce -------------------------

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

//      ------ html funkce ---------------------------

function head(){
    echo('
        <!doctype html>
        <html lang="cs">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <link rel="stylesheet" href="bin/style.css">
        </head>
    ');
}

function navbar(){
    echo '
        <nav>
            <ul class="navbar">
                <li><a class="navbar" href="../index.php">Domů</a></li>
                <li><a class="navbar" href="../posts.php">Příspěvky</a></li>
                <li><a class="navbar" href="../gallery.php">Fotogalerie</a></li>
                <li><a class="navbar" href="../about.php">O nás</a></li>
                <li><a class="navbar" href="../login.php">Přihlásit</a></li>
            </ul>
        </nav>
    ';
}

//      ------- login funkce -------------------------

function login($state){
    while (true) {
        if ($state === STATE_PROCESSED) {
            break;
        } elseif ($state === STATE_FORM_SENT) {
            $data = [];
            $data['login'] = filter_input(INPUT_POST, 'login');
            $data['pass'] = filter_input(INPUT_POST, 'pass');
            $user = getUser($data);
            if (isDataValid($user) && password_verify($data['pass'], $user['pass'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['logged'] = true;
                $_SESSION['ui'] = true;
                redirect(RESULT_SUCCESS);
            } else {
                $state = STATE_FORM_REQUESTED;
                echo("<p class='alert'>Přihlášení se nezdařilo, zkuste to prosím znovu.</p>");
            }
        } else {
            echo("
            <title>Přihlásit</title>
            <div class='contentContainer'>
                <div class='content'>
                    <h1>Přihlášení ke správě stránky</h1>
                    <div class='form'>
                        <form method='post'>
                            <label for='login'>Jméno:  </label>
                            <input type='text' name='login' id='login' required><br>
                            <label for='pass'>Heslo:  </label>
                            <input type='password' name='pass' id='pass' required><br>
                            <input type='hidden' name='action' value='login'>
                            <input type='submit' value='Přihlásit' class='submit'>
                        </form>
                    </div>
                </div>
            </div>
        ");
            break;
        }
    }
}

function getState($logged) : int {
    $result = $logged ? 1 : filter_input(INPUT_GET, 'result', FILTER_VALIDATE_INT);
    if(!$result) {
        $result = 0;
    }
    if ($result === RESULT_SUCCESS) {
        return STATE_PROCESSED;
    }

    $action = filter_input(INPUT_POST, 'action');
    if ($action === 'login') {
        return STATE_FORM_SENT;
    }

    return STATE_FORM_REQUESTED;
}

function isDataValid(array $user) : bool {
    if(isset($user['user_id']) && isset($user['login']) && isset($user['pass'])) {
        return true;
    }
    else{
        return false;
    }
}

function getUser(array $data){
    $user = [];
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT `user_id`, `login`, `pass` FROM `users`
        WHERE `login`='" . $data['login'] . "'");
    foreach ($stmt as $row) {
        $user['user_id'] = ($row['user_id'] == "user_id") ? false : $row['user_id'];
        $user['login'] = ($row['login'] == "login") ? false : $row['login'];
        $user['pass'] = ($row['pass'] == "password") ? false : $row['pass'];
    }
    return $user;
}

function redirect(int $result) : void {
    $location = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $location?result=$result");
    exit;
}

//      ------ post funkce ----------------------------

function getPosts(){
    $posts = [];
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT `post_id`, `title`, `content` FROM `posts`");
    foreach ($stmt as $row) {
        $post['post_id'] = $row['post_id'];
        $post['title'] = $row['title'];
        $post['content'] = $row['content'];
        $posts[$post['post_id']-1] = $post;
    }
    return $posts;
}

function post($post) {
    echo "
        <div class='post'>
            <h2>" . $post['title'] . "</h2>
            <p>" . $post['content'] . "</p>
        </div>
    ";
}

//      ------ gallery funkce --------------------------

function getPhotos(){
    $photos = [];
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT `img_name`, `alt`, `img_id` FROM `imgs`");
    foreach ($stmt as $row) {
        $photo['img_name'] = $row['img_name'];
        $photo['alt'] = $row['alt'];
        $photo['img_id'] = $row['img_id'];
        $photos[$photo['img_id']-1] = $photo;
    }
    return $photos;
}

function photo($photo){
    echo '<img class="gallery" src="img/' . $photo["img_name"] .
        '" alt="' . $photo["alt"] . '" title="' . $photo["alt"] . '">';
}

//      ------ admin funkce ----------------------------

function editWeb($state){
    while (true) {
        if($state === STATE_FORM_NEWPOST_SENT) {
            $data = [];
            $data['title'] = htmlspecialchars(filter_input(INPUT_POST, 'title'));
            $data['content'] = htmlspecialchars(filter_input(INPUT_POST, 'content'));
            if (insertPost($data)) {
                redirect(RESULT_SUCCESS);
            }
            else {
                $state = STATE_FORM_REQUESTED;
                echo("<p class='alert'>Něco se nepovedlo, zkuste to prosím znovu.</p>");
            }
        }
        else if($state === STATE_FORM_NEWPHOTO_SENT){
            $img = $_FILES['img'];
            $path = "img/";
            $name = basename($img['name']);
            $temp = $img['tmp_name'];
            $alt = filter_input(INPUT_POST, 'alt');

            if(move_uploaded_file($temp, $path . $name)) {
                if(insertPhotoData($name, $alt)) redirect(RESULT_SUCCESS);
            }
            else {
                $state = STATE_FORM_REQUESTED;
                echo("<p class='alert'>Něco se nepovedlo, zkuste to prosím znovu.</p>");
            }
        }
        else if($state === STATE_FORM_CHNGPASS_SENT){
            $data = [];
            $data['oldPass'] = htmlspecialchars(filter_input(INPUT_POST, 'oldPass'));
            $data['newPass'] = htmlspecialchars(filter_input(INPUT_POST, 'newPass'));
            $data['newPass2'] = htmlspecialchars(filter_input(INPUT_POST, 'newPass2'));
            if($data['newPass'] == $data['newPass2'] &&
                password_verify($data['oldPass'], getUserById($_SESSION['user_id']))){
                if (updatePass($_SESSION['user_id'], $data['newPass'])) {
                    redirect(RESULT_SUCCESS);
                }
                else {
                    $state = STATE_FORM_REQUESTED;
                    echo("<p class='alert'>Něco se nepovedlo, zkuste to prosím znovu.</p>");
                }
            }
            else {
                $state = STATE_FORM_REQUESTED;
                echo("<p class='alert'>Hesla se neshodují.</p>");
            }
        }
        else {
            echo "
                <title>Úprava webu</title>
                <h1></h1>
                <div class='contentContainer'>
                    <div class='content' id='leftContent'>
                        <div class='admin'>
                            <h2>Nový příspěvek</h2>
                            <div class='form'>
                                <form method='post' id='newPost'>
                                    <label for='title'>Nadpis:  </label><br>
                                    <input type='text' name='title' id='title' required><br>
                                    <label for='content'>Text:  </label><br>
                                    <textarea form='newPost' name='content' id='content'></textarea><br>
                                    <input type='hidden' name='action' value='newPost'>
                                    <input type='submit' value='Přidat' class='submit'>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class='content' id='rightContent'>
                        <div class='admin' id='chngPassContainer'>
                            <h2>Změna hesla</h2>
                            <div class='form'>
                                <form method='post'>
                                    <label for='oldPass'>Stávající heslo:  </label>
                                    <input type='password' name='oldPass' id='oldPass' required><br>
                                    <label for='newPass'>Nové heslo:  </label>
                                    <input type='password' name='newPass' id='newPass' required><br>
                                    <label for='newPass2'>Nové heslo znovu:  </label>
                                    <input type='password' name='newPass2' id='newPass2' required><br>
                                    <input type='hidden' name='action' value='chngPass'>
                                    <input type='submit' value='Změnit heslo' class='submit'>
                                </form>
                            </div>
                        </div>
                        <div class='admin'>
                            <h2>Nová fotografie</h2>
                            <div class='form'>
                                <form method='post' enctype='multipart/form-data'>
                                    <label for='img'>Vyberte nebo přetáhněte fotografii zde:</label><br>
                                    <input type='file' name='img' id='img' accept='image/*' required><br>
                                    <label for='alt'>Přidejte krátký popis fotografie:</label><br>
                                    <input type='text' name='alt' id='alt' required><br>
                                    <input type='hidden' name='action' value='upload'>
                                    <input type='submit' value='Nahrát' class='submit'>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ";
            break;
        }
    }
}

function getForm() : int {
    $action = filter_input(INPUT_POST, 'action');
    if ($action === 'newPost') {
        return STATE_FORM_NEWPOST_SENT;
    }
    else if ($action === 'chngPass') {
        return STATE_FORM_CHNGPASS_SENT;
    }
    else if ($action === 'upload') {
        return STATE_FORM_NEWPHOTO_SENT;
    }

    return STATE_FORM_REQUESTED;
}

function getUserById(int $user_id){
    $pass = null;
    $pdo = dbConnect();
    $stmt = $pdo->query("SELECT `pass` FROM `users`
        WHERE `user_id`='" . $user_id . "'");
    foreach ($stmt as $row) {
        $pass = ($row['pass'] == "pass") ? false : $row['pass'];
    }
    return $pass;
}

function updatePass($user_id, $input) {
    $query = "UPDATE users SET pass = :pass WHERE user_id = :user_id";
    $pdo = dbConnect();
    $stmt = $pdo->prepare($query);
    $hash = password_hash($input, PASSWORD_DEFAULT);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':pass', $hash);

    return $stmt->execute();
}

function insertPost($data) {
    $query = "INSERT INTO `posts` (title, content) VALUES (:title, :content)";
    $pdo = dbConnect();
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':content', $data['content']);

    return $stmt->execute();
}

function insertPhotoData($name, $alt){
    $query = "INSERT INTO `imgs` (img_name, alt) VALUES (:img_name, :alt)";
    $pdo = dbConnect();
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':img_name', $name);
    $stmt->bindParam(':alt', $alt);

    return $stmt->execute();
}