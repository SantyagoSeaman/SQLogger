<?php

namespace SQLogger\Wrapper;

use SQLogger\Storage\StorageAdapterInterface;

interface WrapperInterface
{
    const OPERATION_CONNECT = 1;
    const OPERATION_EXEC = 2;
    const OPERATION_QUERY = 3;
    const OPERATION_PREPARE = 4;
    const OPERATION_STATEMENT_EXECUTE = 5;

    public function wrap();

    /**
     * @param bool $triggered
     * @param int $options
     * @param int $limit
     * @return mixed
     */
    public function setDebugBacktrace($triggered = true, $options = DEBUG_BACKTRACE_IGNORE_ARGS, $limit = 100);

    /**
     * @param StorageAdapterInterface $storage
     * @return mixed
     */
    public function setStorageAdapter(StorageAdapterInterface $storage);
}
