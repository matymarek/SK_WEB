<?php
require 'bin/autoload.php';


final class PostsPage extends BaseDBPage{

    protected Functions $f;

    protected function setUp(): void
    {
        parent::setUp();
        $this->title = "Příspěvky";
        $this->f = new Functions($this->pdo);
    }

    protected function body(): string
    {
        return $this->f->posts();
    }
}
$page = new PostsPage();
$page->render();

// stejně dynamicky jako v galerii by šly přidat obrázky k příspěvkům
// -> obrázek do složky, např. 'postimgs', a do db příspěvků přidat 'post_img' a 'img_alt'