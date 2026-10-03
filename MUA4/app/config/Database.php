<?php
class Database
{
    private static string $host     = 'localhost';
    private static string $username = 'root';
    private static string $password = '';
    private static string $database = 'db_mua2';

    private static ?mysqli $connection = null;

    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            $mysqli = new mysqli(self::$host, self::$username, self::$password, self::$database);
            if ($mysqli->connect_error) {
                die('Koneksi database gagal: ' . $mysqli->connect_error);
            }
            $mysqli->set_charset('utf8mb4');
            self::$connection = $mysqli;
        }
        return self::$connection;
    }
}