<?php
require 'bin/autoload.php';

final class GalleryPage extends BaseDBPage{

    protected Functions $f;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = "Galerie";
        $this->f = new Functions($this->pdo);
    }

    protected function body(): string
    {
        return $this->f->photos();
    }
}
$page = new GalleryPage();
$page->render();