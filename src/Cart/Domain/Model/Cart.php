<?php

declare(strict_types = 1);

namespace Cart\Domain\Model;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Cart\Domain\Event\CartCleared;
use Cart\Domain\Event\CartPickedUp;
use Cart\Domain\Event\CartItemAdded;
use Cart\Domain\Event\CartItemQuantityChanged;
use Cart\Domain\Event\CartItemRemoved;
use Cart\Domain\Exception\CartCurrencyMismatchException;
use Cart\Domain\Exception\CartCurrencyNotSupportedException;
use Cart\Domain\Exception\CartLimitExceeded;
use Cart\Domain\Exception\InvalidCartItemUnitPriceException;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class Cart extends EventSourcedAggregateRoot implements CartContract
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @var CartItemCollection
     */
    private $cartItems;

    /**
     * @var Currency
     */
    private $cartCurrency;

    /**
     * @var AvailableCurrenciesProvider
     */
    private $availableCurrenciesProvider;

    /**
     * @param AvailableCurrenciesProvider $availableCurrenciesProvider
     */
    private function __construct(AvailableCurrenciesProvider $availableCurrenciesProvider)
    {
        $this->availableCurrenciesProvider = $availableCurrenciesProvider;
    }

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
    ): CartContract {
        $cart = new self($availableCurrenciesProvider);

        $cartCurrency = new Currency($currencyCode);

        if (!$cartCurrency->isAvailableWithin($cart->availableCurrenciesProvider->provideAvailableCurrencies())) {
            throw new CartCurrencyNotSupportedException();
        }

        $cart->apply(CartPickedUp::occur($cartId, $cartCurrency));

        return $cart;
    }

    /**
     * @param AvailableCurrenciesProvider $availableCurrenciesProvider
     *
     * @return CartContract
     */
    public static function createWithAdapters(
        AvailableCurrenciesProvider $availableCurrenciesProvider
    ): CartContract {
        return new self($availableCurrenciesProvider);
    }

    /**
     * @param string $productCode
     * @param int $quantity
     * @param int $price
     * @param string $productCurrencyCode
     */
    public function addProductToCart(string $productCode, int $quantity, int $price, string $productCurrencyCode): void
    {
        $price = new Money($price, new Currency($productCurrencyCode));
        $quantity = CartItemQuantity::create($quantity);
        $productCode = ProductCode::fromString($productCode);

        if ($price->isNegative()) {
            throw new InvalidCartItemUnitPriceException('Cart item unit price cannot be below zero.');
        }

        if (!$this->cartCurrency->equals($price->getCurrency())) {
            throw new CartCurrencyMismatchException($this->cartCurrency, $price->getCurrency());
        }

        if (3 === $this->cartItems->count()) {
            throw new CartLimitExceeded($this->cartId);
        }

        $cartItem = CartItem::create(
            $productCode,
            $quantity,
            $price
        );

        $this->apply(CartItemAdded::occur($this->cartId, $cartItem));
    }

    /**
     * @param string $productCode
     */
    public function removeProductFromCart(string $productCode): void
    {
        $cartItem = $this->cartItems->findOneByProductCode(ProductCode::fromString($productCode));

        $this->apply(CartItemRemoved::occur($this->cartId, $cartItem->productCode()));
    }

    public function clear(): void
    {
        if (!$this->cartItems->isEmpty()) {
            $this->apply(CartCleared::occur($this->cartId));
        }
    }

    /**
     * @param string $productCode
     * @param int $quantity
     */
    public function changeProductQuantity(string $productCode, int $quantity): void
    {
        $productCode = ProductCode::fromString($productCode);
        $cartItem = $this->cartItems->findOneByProductCode($productCode);
        $newQuantity = CartItemQuantity::create($quantity);

        $this->apply(CartItemQuantityChanged::occur($this->cartId, $productCode, $cartItem->quantity(), $newQuantity));
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregateRootId(): UuidInterface
    {
        return $this->cartId;
    }

    /**
     * @param CartPickedUp $event
     */
    protected function applyCartPickedUp(CartPickedUp $event): void
    {
        $this->cartId = $event->cartId();
        $this->cartCurrency = $event->cartCurrency();
        $this->cartItems = CartItems::createEmpty();
    }

    /**
     * @param CartItemAdded $event
     */
    protected function applyCartItemAdded(CartItemAdded $event): void
    {
        $this->cartItems->add($event->cartItem());
    }

    /**
     * @param CartItemRemoved $event
     */
    protected function applyCartItemRemoved(CartItemRemoved $event): void
    {
        $cartItem = $this->cartItems->findOneByProductCode($event->productCode());
        $this->cartItems->remove($cartItem);
    }

    /**
     * @param CartCleared $event
     */
    protected function applyCartCleared(CartCleared $event): void
    {
        $this->cartItems->clear();
    }
}
