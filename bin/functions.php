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
            <link rel="stylesheet" href="style.css">
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

function post($post){
    echo"
        <div class='post'>
            <h2>" . $post['title'] . "</h2>
            <p>" . $post['content'] . "</p>
        </div>
    ";
}
