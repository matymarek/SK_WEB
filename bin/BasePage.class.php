<?php


abstract class BasePage
{
    protected SessionStorage $sesionStorage;
    protected MustacheRunner $m;
    protected string $title;

    public function __construct() {
        $this->m = new MustacheRunner();
        $this->sesionStorage = new SessionStorage();
    }

    public function render() {
        try {
            $this->setUp();
            $html = $this->header();
            $html .= $this->navbar();
            $html .= $this->body();
            $html .= $this->footer();
            echo $html;
            $this->wrapUp();
        } catch (Exception $e) {
            $ePage = new ErrorPage();
            $ePage->render();
        }
        exit;
    }

    protected function setUp() : void {

    }

    protected function header() : string {
        return $this->m->render("head", ["title" => $this->title]);
    }

    protected function navbar() : string{
        return $this->m->render("navbar", ["admin"=> $_SESSION['login'] ?? "Přihlášení"]);
    }

    protected abstract function body() : string;

    protected function footer() : string {
        return $this->m->render("foot");
    }

    protected function wrapUp() : void {}

}