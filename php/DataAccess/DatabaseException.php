<?php

class DatabaseException extends Exception
{
    protected $query;

    public function __construct($message, $code = 0, Exception $previous = null, $query = null)
    {
        parent::__construct($message, $code, $previous);
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
