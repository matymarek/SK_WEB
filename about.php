<?php
require 'bin/autoload.php';

final class AboutPage extends BasePage{

    protected Functions $f;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = "O nás";
        $this->f = new Functions(null);
    }

    protected function body(): string
    {
        return $this->m->render("about", ["apiKey"=>LocalConfig::API_KEY]);
    }
}
$page = new AboutPage();
$page->render();
