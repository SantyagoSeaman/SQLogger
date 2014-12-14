<?php

namespace SQLogger\Storage;

class StubAdapter implements StorageAdapterInterface
{
    public function store(
        $timeStart,
        $timeFinish,
        $startMemoryUsage,
        $finishMemoryUsage,
        $operationCode,
        $operationName,
        $params = [],
        $additionalInfo = '',
        $trace = []
    ) {
        // Nothing to do
    }
}
