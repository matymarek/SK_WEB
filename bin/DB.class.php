<?php


class DB
{
    private static ?PDO $connection = null;

    private function __construct(){}

    /**
     * @throws Exception
     */
    public static function getConection() : ?PDO {
        if(!self::$connection)
            self::connect();

        if(!self::$connection)
            throw new Exception("Uneble to connect databese.");

        return self::$connection;
    }

    private static function connect(){
        $dsn = "mysql:host=". LocalConfig::DBSERVER .";dbname=". LocalConfig::DBDATABASE .";charset=".Config::DBCHARSET;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$connection = new PDO($dsn, LocalConfig::DBUSER, LocalConfig::DBPASSWORD, $options);
    }
}