<?php


namespace OCA\Projects\Exception;


use Throwable;

class ProjectsRootAlreadyExistsException extends \Exception
{

    /**
     * ProjectsRootAlreadyExistsException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'The projects root already exists';
        }
        parent::__construct($message, $code, $previous);
    }
}