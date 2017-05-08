<?php

declare(strict_types = 1);

namespace Cart\Domain\Exception;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductCodeCannotBeEmptyException extends \DomainException
{
    /**
     * @param string $message
     * @param \Exception|null $previousException
     */
    public function __construct(string $message = 'Product code cannot be empty.', \Exception $previousException = null)
    {
        parent::__construct($message, 0, $previousException);
    }
}
