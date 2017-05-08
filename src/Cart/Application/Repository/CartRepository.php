<?php

declare(strict_types=1);

namespace Cart\Application\Repository;

use Cart\Domain\Model\CartContract;
use Cart\Infrastructure\Exception\CartNotFoundException;
use Ramsey\Uuid\UuidInterface;

interface CartRepository
{
    /**
     * @param UuidInterface $cartId
     *
     * @return CartContract
     *
     * @throws CartNotFoundException
     */
    public function load(UuidInterface $cartId): CartContract;

    /**
     * @param CartContract $cart
     */
    public function save(CartContract $cart): void;
}
