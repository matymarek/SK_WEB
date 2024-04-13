<?php
require 'bin/autoload.php';

final class AdminPage extends BaseDBPage{

    protected Functions $f;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = "Přihlášení";
        $this->f = new Functions($this->pdo);
    }

    protected function body(): string
    {
        $ui = isset($_SESSION['ui']);
        if($ui){
            $this->title = $_SESSION['login'];
            return $this->f->editWeb($this->f->getForm());
        }
        return $this->f->login($this->f->getState(isset($_SESSION['logged'])));
    }
}
$page = new AdminPage();
$page->render();