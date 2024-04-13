<?php
require 'bin/autoload.php';

final class HomePage extends BaseDBPage{

    protected Functions $f;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = "Vítejte";
        $this->f = new Functions($this->pdo);
    }

    protected function body(): string
    {
        return $this->m->render("index", ["photo" => $this->f->photo(2)]);
    }
}
$page = new HomePage();
$page->render();
