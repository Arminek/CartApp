<?php

declare(strict_types = 1);

namespace Cart\Domain\Model;

use Broadway\Domain\AggregateRoot;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface CartContract extends AggregateRoot
{
    /**
     * @param UuidInterface $cartId
     * @param string $currencyCode
     * @param AvailableCurrenciesProvider $availableCurrenciesProvider
     *
     * @return CartContract
     */
    public static function pickUp(
        UuidInterface $cartId,
        string $currencyCode,
        AvailableCurrenciesProvider $availableCurrenciesProvider
    ): CartContract;

    /**
     * @param AvailableCurrenciesProvider $availableCurrenciesProvider
     *
     * @return CartContract
     */
    public static function createWithAdapters(
        AvailableCurrenciesProvider $availableCurrenciesProvider
    ): CartContract;

    /**
     * @param string $productCode
     * @param int $quantity
     * @param int $price
     *
     * @param string $productCurrencyCode
     */
    public function addProductToCart(string $productCode, int $quantity, int $price, string $productCurrencyCode): void;

    /**
     * @param string $productCode
     */
    public function removeProductFromCart(string $productCode): void;

    public function clear(): void;

    /**
     * @param string $productCode
     * @param int $quantity
     */
    public function changeProductQuantity(string $productCode, int $quantity): void;
}
