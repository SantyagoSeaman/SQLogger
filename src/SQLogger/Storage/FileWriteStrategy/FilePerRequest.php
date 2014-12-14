<?php

namespace SQLogger\Storage\FileWriteStrategy;

use SQLogger\Storage\StorageAdapterException;

class FilePerRequest implements FileWriteStrategyInterface
{
    /** @var string */
    protected $fileName;

    public function __construct($pathToCatalog)
    {
        $realPath = realpath($pathToCatalog);
        if (false === $realPath) {
            if (false == mkdir($pathToCatalog, 0777, true)) {
                throw new \RuntimeException('Can\'t create catalog for logger files');
            }
            $realPath = realpath($pathToCatalog);
        }

        // TODO: Add mask formatter
        $this->fileName = $realPath
            . DIRECTORY_SEPARATOR
            . (microtime(true) . '_' . getmypid())
            . '.log';
    }

    /**
     * @param string $data
     * @throws StorageAdapterException
     */
    public function write($data)
    {
        // Additional space between function calls
        $data .= PHP_EOL . PHP_EOL;

        if (file_put_contents($this->fileName, $data, FILE_APPEND | LOCK_EX) === false) {
            throw new StorageAdapterException('Can\'t write to file!');
        }
    }
}
