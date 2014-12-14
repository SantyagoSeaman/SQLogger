<?php

namespace SQLogger\Filter;

class CookieFilter implements FilterInterface
{
    /** @var string */
    protected $cookieName;

    /** @var string */
    protected $cookieValue;

    public function __construct($cookieName, $cookieValue = '')
    {
        $this->cookieName = $cookieName;
        $this->cookieValue = $cookieValue;
    }

    public function isPassed()
    {
        $returnFlag = false;

        if (isset($_COOKIE[$this->cookieName])) {
            $returnFlag = empty($this->cookieValue) ||
                $this->cookieValue === $_COOKIE[$this->cookieName];
        }

        return $returnFlag;
    }
}
