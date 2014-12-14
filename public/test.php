<?php

class Test
{
    public static function fn()
    {
        $db = new PDO('mysql:host=localhost;port=3316;dbname=testdb', 'root', '');
        self::exec($db);
    }

    public static function exec($db)
    {
        $db->exec('SELECT * FROM test_table');
    }
}
