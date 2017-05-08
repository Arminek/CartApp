<?php

declare(strict_types=1);

namespace Cart\Domain\Exception;

use Ramsey\Uuid\UuidInterface;

final class CartLimitExceeded extends \DomainException
{
    /**
     * @param UuidInterface $cartId
     * @param \Exception|null $previousException
     */
    public function __construct(UuidInterface $cartId, \Exception $previousException = null)
    {
        parent::__construct(sprintf('Cart with id "%s" reaches item limit. Current limit is 3.', $cartId), 0, $previousException);
    }
}
