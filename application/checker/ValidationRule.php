<?php
namespace application\checker;

/**
 * Class ValidationRule
 * @package application\checker
 */
class ValidationRule
{
    /**
     * @var string
     */
    protected $regularExpression;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param string $regexp
     * @param string $message
     */
    public function __construct($regexp, $message)
    {
        $this->setRegularExpression($regexp);
        $this->setErrorMessage($message);
    }

    /**
     * @return string
     */
    public function getRegularExpression()
    {
        return $this->regularExpression;
    }

    /**
     * @param string $regexp
     */
    public function setRegularExpression($regexp)
    {
        $this->regularExpression = $regexp;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $message
     */
    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
    }
} 