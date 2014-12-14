<?php

namespace SQLogger\Storage;

use SQLogger\Storage\FileWriteStrategy\FileWriteStrategyInterface;

class FileAdapter implements StorageAdapterInterface
{
    /** @var FileWriteStrategyInterface */
    protected $writeStrategy;

    // TODO: Add Rotator strategy
    public function __construct(FileWriteStrategyInterface $writeStrategy)
    {
        $this->writeStrategy = $writeStrategy;
    }

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
    ) {
        $data = 'Operation: ' . $operationName . PHP_EOL;
        $data .= 'Code: ' . $operationCode . PHP_EOL;
        if ($queryNumber > 0) {
            $data .= 'Query number: ' . $queryNumber . PHP_EOL;
        }
        $data .= 'Started: ' . $this->getReadableTime($timeStart) . PHP_EOL;
        $data .= 'Finished: ' . $this->getReadableTime($timeFinish) . PHP_EOL;
        $data .= 'Duration in sec: ' . round($timeFinish - $timeStart, 6) . PHP_EOL;
        $data .= 'Duration in msec: ' . round(($timeFinish - $timeStart)*1000000, 4) . PHP_EOL;
        $data .= 'Start memory usage: ' . $startMemoryUsage . PHP_EOL;
        $data .= 'Finish memory usage: ' . $finishMemoryUsage . PHP_EOL;
        $data .= 'Memory usage diff: ' . ($finishMemoryUsage - $startMemoryUsage) . PHP_EOL;

        if (false == empty($params)) {
            $data .= 'Parameters:' . PHP_EOL;
            foreach ($params as $index => $param) {
                $data .= "[" . $index . '] => ';
                if (is_array($param)) {
                    $data .= print_r($param, true);
                } else {
                    $data .= $param . PHP_EOL;
                }
            }
        }

        if ($additionalInfo) {
            $data .= 'Additional info: ' . $additionalInfo . PHP_EOL;
        }

        if (false == empty($trace)) {
            $data .= 'Trace:' . PHP_EOL;
            $data .= print_r($trace, true);
        }

        $this->writeStrategy->write($data);
    }

    protected function getReadableTime($time)
    {
        return date("Y-m-d H:i:s", $time) . '.' . round(($time - intval($time))*1000000, 6);
    }
}
