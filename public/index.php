<?php
$_COOKIE['SQLogger'] = true;

include __DIR__ . '/../vendor/autoload.php';

$logger = \SQLogger\SQLogger::init(
    [new \SQLogger\Wrapper\MySQL\PDOWrapper(), new \SQLogger\Wrapper\MySQL\MysqliWrapper()],
    [new \SQLogger\Filter\CookieFilter('SQLogger')],
    new \SQLogger\Storage\FileAdapter(new \SQLogger\Storage\FileWriteStrategy\FilePerRequest('logs'))
);
/*
$logger->setDebugBacktrace(
    true,
    DEBUG_BACKTRACE_PROVIDE_OBJECT,
    1000
);
*/

echo 'Init!';

include 'test.php';
Test::fn();

echo 'Connected!';
