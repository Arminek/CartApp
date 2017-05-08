<?php

declare(strict_types = 1);

namespace Cart\Application\Command;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ClearCart
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @param UuidInterface $cartId
     */
    private function __construct(UuidInterface $cartId)
    {
        $this->cartId = $cartId;
    }

    /**
     * @param UuidInterface $cartId
     *
     * @return ClearCart
     */
    public static function create(UuidInterface $cartId): self
    {
        return new self($cartId);
    }

    /**
     * @return UuidInterface
     */
    public function cartId(): UuidInterface
    {
        return $this->cartId;
    }
}
