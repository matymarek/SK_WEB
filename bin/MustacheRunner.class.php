<?php
require 'vendor/autoload.php';

class MustacheRunner
{
    private $engine;

    public function __construct(){
        Mustache_Autoloader::register();
        $this->engine = new Mustache_Engine([
            "entiti_flags" => ENT_QUOTES,
            "loader" => new Mustache_Loader_FilesystemLoader(__DIR__ . "/../" . Config::TEMPLATESDIR)
        ]);
    }

    public function render($templateName, $context = []){
        return $this->engine->loadTemplate($templateName)->render($context);
    }
}