<?php

namespace SQLogger\Wrapper\MySQL;

use SQLogger\Wrapper\WrapperAbstract;

class PDOWrapper extends WrapperAbstract
{
    public function wrap()
    {
        $this->redefineMethod('PDO', '__construct', self::OPERATION_CONNECT);
        $this->redefineMethod('PDO', 'exec', self::OPERATION_EXEC);
        $this->redefineMethod('PDO', 'query', self::OPERATION_EXEC);
        $this->redefineMethod('PDO', 'prepare', self::OPERATION_PREPARE);
        $this->redefineMethod('PDOStatement', 'execute', self::OPERATION_STATEMENT_EXECUTE);

        /*
        uopz_rename(PDOStatement::class, "fetch", "original_fetch");
        uopz_function(PDOStatement::class, "fetch", function ($fetch_style = PDO::FETCH_BOTH, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
                $time = microtime(true);
                $this->original_fetch($fetch_style, $cursor_orientation, $cursor_offset);

                file_put_contents('aaa.txt', '--statement fetch' . PHP_EOL . (microtime(true) - $time)*1000 . PHP_EOL . PHP_EOL, FILE_APPEND);
        });


        uopz_rename(PDOStatement::class, "fetchAll", "original_fetchAll");
        uopz_function(PDOStatement::class, "fetchAll", function ($fetch_style = PDO::FETCH_BOTH) {
                $time = microtime(true);
                $this->original_fetchAll($fetch_style);

                file_put_contents('aaa.txt', '--statement fetchAll' . PHP_EOL . (microtime(true) - $time)*1000 . PHP_EOL . PHP_EOL, FILE_APPEND);
        });
        */

    }
}
