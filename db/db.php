<?php

class db {
    public static function connect() {

        $host = 'db'; 
        $dbname = getenv('MYSQL_NAME');

        try {
            $dsn = "mysql:host=$host;dbname=$dbname";
            
            $dbh = new PDO($dsn, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));

            return $dbh;

        } catch (PDOException $e) {
            $e->getMessage();
        }
    }
}