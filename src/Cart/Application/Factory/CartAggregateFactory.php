<?php

declare(strict_types = 1);

namespace Cart\Application\Factory;

use Broadway\Domain\DomainEventStream;
use Cart\Domain\Model\AvailableCurrenciesProvider;
use Cart\Domain\Model\Cart;
use Cart\Domain\Model\CartContract;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class CartAggregateFactory implements CartFactory
{
    /**
     * @var AvailableCurrenciesProvider
     */
    private $availableCurrenciesProvider;

    /**
     * @param AvailableCurrenciesProvider $availableCurrenciesProvider
     */
    public function __construct(AvailableCurrenciesProvider $availableCurrenciesProvider)
    {
        $this->availableCurrenciesProvider = $availableCurrenciesProvider;
    }

    /**
     * @param string $aggregateClass
     * @param DomainEventStream $domainEventStream
     *
     * @return CartContract
     */
    public function create($aggregateClass, DomainEventStream $domainEventStream): CartContract
    {
        /** @var Cart $cart */
        $cart = Cart::createWithAdapters($this->availableCurrenciesProvider);

        $cart->initializeState($domainEventStream);

        return $cart;
    }

    /**
     * @param UuidInterface $cartId
     * @param string $currencyCode
     *
     * @return CartContract
     */
    public function pickUp(UuidInterface $cartId, string $currencyCode): CartContract
    {
        return Cart::pickUp($cartId, $currencyCode, $this->availableCurrenciesProvider);
    }
}
