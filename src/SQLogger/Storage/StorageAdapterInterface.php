<?php

namespace SQLogger\Storage;

interface StorageAdapterInterface
{
    /**
     * @param float $timeStart
     * @param float $timeFinish
     * @param int $startMemoryUsage
     * @param int $finishMemoryUsage
     * @param int $queryNumber
     * @param int $operationCode
     * @param string $operationName
     * @param array $params
     * @param string $additionalInfo
     * @param array $trace
     * @return mixed
     */
    public function store(
        $timeStart,
        $timeFinish,
        $startMemoryUsage,
        $finishMemoryUsage,
        $queryNumber,
        $operationCode,
        $operationName,
        $params = [],
        $additionalInfo = '',
        $trace = []
    );
}
