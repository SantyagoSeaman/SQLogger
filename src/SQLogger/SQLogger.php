<?php

namespace SQLogger;

use SQLogger\Filter\FilterInterface;
use SQLogger\Storage\StorageAdapterInterface;
use SQLogger\Wrapper\WrapperInterface;

class SQLogger
{
    /** @var SQLogger */
    protected static $loggerInstance;

    /** @var array */
    protected $wrapperCollection;

    /** @var array */
    protected $filterCollection;

    /** @var StorageAdapterInterface */
    protected $storage;

    protected function __construct()
    {
    }

    /**
     * @param array $wrapperCollection
     * @param array $filterCollection
     * @param StorageAdapterInterface $storage
     * @return bool|SQLogger
     */
    public static function init(
        $wrapperCollection,
        $filterCollection,
        StorageAdapterInterface $storage
    ) {
        if (false === empty(self::$loggerInstance)) {
            return self::$loggerInstance;
        }

        $continueFlag = true;
        if (empty($filterCollection) === false) {
            $continueFlag = false;
            /** @var FilterInterface $filter */
            foreach ($filterCollection as $filter) {
                if ($filter->isPassed()) {
                    $continueFlag = true;
                    break;
                }
            }
        }

        if (!$continueFlag) {
            return false;
        }

        if (empty($wrapperCollection) === false) {
            /** @var WrapperInterface $wrapper */
            foreach ($wrapperCollection as $wrapper) {
                $wrapper->setStorageAdapter($storage);
                $wrapper->wrap();
            }
        }

        $loggerInstance = new SQLogger();
        self::$loggerInstance = $loggerInstance;
        $loggerInstance->wrapperCollection = $wrapperCollection;
        $loggerInstance->filterCollection = $filterCollection;
        $loggerInstance->storage = $storage;

        return self::$loggerInstance;
    }

    public function setDebugBacktrace($triggered = true, $options = DEBUG_BACKTRACE_IGNORE_ARGS, $limit = 100)
    {
        /** @var WrapperInterface $wrapper */
        foreach ($this->wrapperCollection as $wrapper) {
            $wrapper->setDebugBacktrace($triggered, $options, $limit);
        }
    }
}
