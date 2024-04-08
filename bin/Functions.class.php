<?php
include 'MustacheRunner.class.php';
class Functions{
    private MustacheRunner $m;

    //      ------ init ------------------------------------

    public function __construct(){
        $this->m = new MustacheRunner();
    }

    //      ------ obecné funkce ---------------------------

    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    //      ------ html funkce -----------------------------

    function head($title){
        $this->m->render("head", ["title" => $title]);
    }

    function navbar(){
        $this->m->render("navbar");
    }

    function body($name, $properties = []){
        $this->m->render($name, $properties);
    }

    function error($name, $message){
        $this->m->render($name, ["message" => $message]);
    }

    //      ------- login funkce ---------------------------

    function login($state){
        while (true) {
            if ($state === STATE_PROCESSED) {
                break;
            } elseif ($state === STATE_FORM_SENT) {
                $data = [];
                $data['login'] = filter_input(INPUT_POST, 'login');
                $data['pass'] = filter_input(INPUT_POST, 'pass');
                $user = $this->getUser($data);
                if ($this->isDataValid($user) && password_verify($data['pass'], $user['pass'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['logged'] = true;
                    $_SESSION['ui'] = true;
                    $this->redirect(RESULT_SUCCESS);
                } else {
                    $state = STATE_FORM_REQUESTED;
                    $this->error("errorBox", "Přihlášení se nezdařilo, zkuste to prosím znovu.");
                }
            } else {
                $this->head("Přihlásit");
                $this->body("login");
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

    //      ------ post funkce -----------------------------

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

    function posts() {
        $this->m->render("posts", ["posts" => $this->getPosts()]);
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

    function photos(){
        $this->m->render("gallery", ["photos" => $this->getPhotos()]);
    }

    //      ------ admin funkce ----------------------------

    function editWeb($state){
        while (true) {
            if($state === STATE_FORM_NEWPOST_SENT) {
                $data = [];
                $data['title'] = htmlspecialchars(filter_input(INPUT_POST, 'title'));
                $data['content'] = htmlspecialchars(filter_input(INPUT_POST, 'content'));
                if ($this->insertPost($data)) {
                    $this->redirect(RESULT_SUCCESS);
                }
                else {
                    $state = STATE_FORM_REQUESTED;
                    $this->error("errorBox", "Něco se nepovedlo, zkuste to prosím znovu.");
                }
            }
            else if($state === STATE_FORM_NEWPHOTO_SENT){
                $img = $_FILES['img'];
                $path = "img/";
                $name = basename($img['name']);
                $temp = $img['tmp_name'];
                $alt = filter_input(INPUT_POST, 'alt');

                if(move_uploaded_file($temp, $path . $name)) {
                    if($this->insertPhotoData($name, $alt)) $this->redirect(RESULT_SUCCESS);
                }
                else {
                    $state = STATE_FORM_REQUESTED;
                    $this->error("errorBox", "Něco se nepovedlo, zkuste to prosím znovu.");
                }
            }
            else if($state === STATE_FORM_CHNGPASS_SENT){
                $data = [];
                $data['oldPass'] = htmlspecialchars(filter_input(INPUT_POST, 'oldPass'));
                $data['newPass'] = htmlspecialchars(filter_input(INPUT_POST, 'newPass'));
                $data['newPass2'] = htmlspecialchars(filter_input(INPUT_POST, 'newPass2'));
                if($data['newPass'] == $data['newPass2'] &&
                    password_verify($data['oldPass'], $this->getUserById($_SESSION['user_id']))){
                    if ($this->updatePass($_SESSION['user_id'], $data['newPass'])) {
                        $this->redirect(RESULT_SUCCESS);
                    }
                    else {
                        $state = STATE_FORM_REQUESTED;
                        $this->error("errorBox", "Něco se nepovedlo, zkuste to prosím znovu.");
                    }
                }
                else {
                    $state = STATE_FORM_REQUESTED;
                    $this->error("errorBox", "Hesla se neshodují.");
                }
            }
            else {
                $this->head("Úprava webu");
                $this->body("editWeb");
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

    function insertPhotoData($name, $alt)
    {
        $query = "INSERT INTO `imgs` (img_name, alt) VALUES (:img_name, :alt)";
        $pdo = dbConnect();
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':img_name', $name);
        $stmt->bindParam(':alt', $alt);

        return $stmt->execute();
    }
}