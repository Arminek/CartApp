<?php

declare(strict_types = 1);

namespace Cart\Application\Factory;

use Broadway\EventSourcing\AggregateFactory\AggregateFactory;
use Cart\Domain\Model\CartContract;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface CartFactory extends AggregateFactory
{
    /**
     * @param UuidInterface $cartId
     * @param string $currencyCode
     *
     * @return CartContract
     */
    public function pickUp(UuidInterface $cartId, string $currencyCode): CartContract;
}
