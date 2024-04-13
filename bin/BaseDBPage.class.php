<?php


abstract class BaseDBPage extends BasePage
{
    protected ?PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->pdo = DB::getConection();
        } catch (Exception $e) {
            $ePage = new ErrorPage();
            $ePage->render();
        }
    }

}