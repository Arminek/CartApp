<?php

declare(strict_types=1);

namespace Tests\Cart\Domain\Event;

use Cart\Domain\Event\CartCleared;
use Ramsey\Uuid\Uuid;

final class CartClearedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_immutable_fact_of_clearing_cart(): void
    {
        $cartId = Uuid::uuid4();
        $event = CartCleared::occur($cartId);

        $this->assertEquals($cartId, $event->cartId());
    }
}
