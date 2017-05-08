<?php

declare(strict_types = 1);

namespace Cart\Domain\Model;

use Cart\Domain\Exception\CartItemNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface CartItemCollection extends \Countable, \IteratorAggregate
{
    /**
     * @param CartItem $cartItem
     */
    public function add(CartItem $cartItem): void;

    /**
     * @param CartItem $cartItem
     *
     * @throws CartItemNotFoundException
     */
    public function remove(CartItem $cartItem): void;

    /**
     * @param ProductCode $productCode
     *
     * @return CartItem
     *
     * @throws CartItemNotFoundException
     */
    public function findOneByProductCode(ProductCode $productCode): CartItem;

    /**
     * @param CartItem $cartItem
     *
     * @return bool
     */
    public function exists(CartItem $cartItem): bool;

    /**
     * @return array
     */
    public function findAll(): array;

    public function clear(): void;

    public function isEmpty(): bool;
}
