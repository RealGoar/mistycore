<?php
namespace Jacob\Orix\exception;

use RuntimeException;
use Throwable;

class TranslationFailedException extends RuntimeException {

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}