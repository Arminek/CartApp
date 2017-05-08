<?php

declare(strict_types=1);

namespace Cart\Infrastructure\Exception;

use Ramsey\Uuid\UuidInterface;

final class CartNotFoundException extends \RuntimeException
{
    /**
     * @param UuidInterface $cartId
     * @param \Exception|null $previousException
     *
     * @return CartNotFoundException
     */
    public static function withId(UuidInterface $cartId, \Exception $previousException = null): self
    {
        return new self(sprintf('Cart with "%s" does not exist.', $cartId), 0, $previousException);
    }
}
