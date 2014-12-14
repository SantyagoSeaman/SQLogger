<?php

namespace SQLogger\Wrapper;

use SQLogger\Storage\StorageAdapterInterface;

abstract class WrapperAbstract implements WrapperInterface
{
    /** @var bool */
    protected $backtraceTriggered = true;
    /** @var int */
    protected $backtraceOptions = DEBUG_BACKTRACE_IGNORE_ARGS;
    /** @var int */
    protected $backtraceLimit = 100;
    /** @var StorageAdapterInterface */
    protected $storageAdapter;

    protected $queryCounter = 0;

    /**
     * @param bool $triggered
     * @param int $options
     * @param int $limit
     * @return $this
     */
    public function setDebugBacktrace($triggered = true, $options = DEBUG_BACKTRACE_IGNORE_ARGS, $limit = 100)
    {
        $this->backtraceTriggered = $triggered;
        $this->backtraceOptions = $options;
        $this->backtraceLimit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getQueryCounter()
    {
        return $this->queryCounter;
    }

    /**
     * @param int $totalCounter
     * @return $this
     */
    public function setQueryCounter($totalCounter)
    {
        $this->queryCounter = $totalCounter;
        return $this;
    }

    /**
     * @return int
     */
    public function incrementQueryCounter()
    {
        $this->queryCounter ++;
        return $this->queryCounter;
    }

    /**
     * @param StorageAdapterInterface $storage
     * @return $this
     */
    public function setStorageAdapter(StorageAdapterInterface $storage)
    {
        $this->storageAdapter = $storage;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getBacktraceTriggered()
    {
        return $this->backtraceTriggered;
    }

    /**
     * @return int
     */
    public function getBacktraceOptions()
    {
        return $this->backtraceOptions;
    }

    /**
     * @return int
     */
    public function getBacktraceLimit()
    {
        return $this->backtraceLimit;
    }

    /**
     * @return \SQLogger\Storage\StorageAdapterInterface
     */
    public function getStorageAdapter()
    {
        return $this->storageAdapter;
    }

    protected function redefineMethod(
        $className,
        $methodName,
        $operationCode,
        callable $additionalInfoCallback = null
    ) {
        $modifiedMethodName = $methodName . '_original';
        $caller = $this;

        // TODO: Implement version with Runkit functions
        uopz_rename($className, $methodName, $modifiedMethodName);
        uopz_function(
            $className,
            $methodName,
            function () use (
                $caller,
                $className,
                $methodName,
                $operationCode,
                $modifiedMethodName,
                $additionalInfoCallback
            ) {
                $storage = $caller->getStorageAdapter();
                $backtraceTriggered = $caller->getBacktraceTriggered();
                $backtraceOptions = $caller->getBacktraceOptions();
                $backtraceLimit = $caller->getBacktraceLimit();

                $origParams = [];
                $funcParams = [];
                if (func_num_args() > 0) {
                    $origParams = func_get_args();
                    $funcParams = $origParams;
                }

                if (WrapperInterface::OPERATION_CONNECT == $operationCode) {
                    // Remove login-password and leave only DSN
                    if (false == empty($origParams)) {
                        $origParams = [$origParams[0]];
                    }
                    $backtraceOptions |= DEBUG_BACKTRACE_IGNORE_ARGS;
                }

                if (WrapperInterface::OPERATION_PREPARE == $operationCode
                    || WrapperInterface::OPERATION_EXEC == $operationCode
                ) {
                    if (false == empty($funcParams)) {
                        $funcParams[0] = "/* additional info to find queries in slow log */\n" . $funcParams[0];
                    }
                }

                $queryNumber = 0;
                if (WrapperInterface::OPERATION_EXEC == $operationCode
                    || WrapperInterface::OPERATION_STATEMENT_EXECUTE == $operationCode
                ) {
                    $queryNumber = $caller->incrementQueryCounter();
                }

                $startMemoryUsage = memory_get_usage();
                $startTime = microtime(true);

                $result = call_user_func_array([$this, $modifiedMethodName], $funcParams);

                $finishTime = microtime(true);
                $finishMemoryUsage = memory_get_usage();

                $additionalInfo = '';
                if ($additionalInfoCallback) {
                    $additionalInfo = $additionalInfoCallback($this, $origParams, $result, $startTime, $finishTime);
                }

                $trace = [];
                if ($backtraceTriggered) {
                    $trace = debug_backtrace($backtraceOptions, $backtraceLimit);
                    if (isset($trace[0]['function'])) {
                        $trace[0]['function'] = $methodName;
                    }
                }

                $storage->store(
                    $startTime,
                    $finishTime,
                    $startMemoryUsage,
                    $finishMemoryUsage,
                    $queryNumber,
                    $operationCode,
                    $className . '::' . $methodName,
                    $origParams,
                    $additionalInfo,
                    $trace
                );

                return $result;
            }
        );
    }
}
