<?php

declare(strict_types=1);

namespace Tests\Cart\Domain\Model;

use Broadway\EventSourcing\Testing\AggregateRootScenarioTestCase;
use Cart\Application\Adapter\AvailableCurrencies\ISOCurrenciesProvider;
use Cart\Application\Factory\CartAggregateFactory;
use Cart\Application\Factory\CartFactory;
use Cart\Domain\Event\CartCleared;
use Cart\Domain\Event\CartItemAdded;
use Cart\Domain\Event\CartItemQuantityChanged;
use Cart\Domain\Event\CartItemRemoved;
use Cart\Domain\Event\CartPickedUp;
use Cart\Domain\Exception\CartCurrencyMismatchException;
use Cart\Domain\Exception\CartCurrencyNotSupportedException;
use Cart\Domain\Exception\CartItemNotFoundException;
use Cart\Domain\Exception\CartLimitExceeded;
use Cart\Domain\Exception\InvalidCartItemQuantityException;
use Cart\Domain\Exception\InvalidCartItemUnitPriceException;
use Cart\Domain\Exception\ProductCodeCannotBeEmptyException;
use Cart\Domain\Model\Cart;
use Cart\Domain\Model\CartContract;
use Cart\Domain\Model\CartItem;
use Cart\Domain\Model\CartItemQuantity;
use Cart\Domain\Model\ProductCode;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

final class CartTest extends AggregateRootScenarioTestCase
{
    /**
     * @test
     */
    public function it_can_be_pick_up(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $ISOCurrenciesProvider = new ISOCurrenciesProvider();

        $this->scenario
            ->when(function () use ($cartId, $cartCurrency, $ISOCurrenciesProvider) {
                return Cart::pickUp($cartId, $cartCurrency->getCode(), $ISOCurrenciesProvider);
            })
            ->then([
                CartPickedUp::occur($cartId, $cartCurrency)
            ])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_be_pick_up_for_unsupported_currency(): void
    {
        $this->expectException(CartCurrencyNotSupportedException::class);
        $ISOCurrenciesProvider = new ISOCurrenciesProvider();
        $cartId = Uuid::uuid4();

        $this->scenario
            ->when(function () use ($cartId, $ISOCurrenciesProvider) {
                return Cart::pickUp($cartId, 'ABC', $ISOCurrenciesProvider);
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_can_store_products(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $cartItem = CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000));

        $this->scenario
            ->given([CartPickedUp::occur($cartId, $cartCurrency)])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('1', 2, 1000, 'USD');
            })
            ->then([
                CartItemAdded::occur($cartId, $cartItem)
            ])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_add_product_in_different_currencies(): void
    {
        $this->expectException(CartCurrencyMismatchException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([CartPickedUp::occur($cartId, $cartCurrency)])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('1', 2, 1000, 'EUR');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_add_product_with_negative_price(): void
    {
        $this->expectException(InvalidCartItemUnitPriceException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([CartPickedUp::occur($cartId, $cartCurrency)])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('1', 2, -1000, 'USD');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_add_product_with_empty_product_code(): void
    {
        $this->expectException(ProductCodeCannotBeEmptyException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([CartPickedUp::occur($cartId, $cartCurrency)])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('', 2, -1000, 'USD');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_add_product_with_zero_quantity(): void
    {
        $this->expectException(InvalidCartItemQuantityException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([CartPickedUp::occur($cartId, $cartCurrency)])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('2', 0, -1000, 'USD');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_add_product_with_quantity_below_zero(): void
    {
        $this->expectException(InvalidCartItemQuantityException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([CartPickedUp::occur($cartId, $cartCurrency)])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('2', -10, -1000, 'USD');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_add_another_product_if_there_are_3_items(): void
    {
        $this->expectException(CartLimitExceeded::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('3'), CartItemQuantity::create(4), Money::USD(1500))
                ),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('6'), CartItemQuantity::create(1), Money::USD(1300))
                )
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('2', 1, 1400, 'USD');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_can_add_another_product_if_one_slot_free_up(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('3'), CartItemQuantity::create(4), Money::USD(1500))
                ),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('6'), CartItemQuantity::create(1), Money::USD(1300))
                ),
                CartItemRemoved::occur($cartId, ProductCode::fromString('6')),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('2', 1, 1400, 'USD');
            })
            ->then([
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('2'), CartItemQuantity::create(1), Money::USD(1400))
                )
            ])
        ;
    }

    /**
     * @test
     */
    public function it_can_add_another_product_if_there_are_only_2_items(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');

        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('3'), CartItemQuantity::create(4), Money::USD(1500))
                ),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->addProductToCart('2', 1, 1400, 'USD');
            })
            ->then([
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('2'), CartItemQuantity::create(1), Money::USD(1400))
                )
            ])
        ;
    }

    /**
     * @test
     */
    public function it_can_remove_product(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                )
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->removeProductFromCart('1');
            })
            ->then([
                CartItemRemoved::occur($cartId, ProductCode::fromString('1'))
            ])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_remove_product_which_does_not_exist(): void
    {
        $this->expectException(CartItemNotFoundException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                )
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->removeProductFromCart('3');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_remove_product_with_empty_product_code(): void
    {
        $this->expectException(ProductCodeCannotBeEmptyException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                )
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->removeProductFromCart('');
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_can_be_cleared(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                )
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->clear();
            })
            ->then([
                CartCleared::occur($cartId)
            ])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_be_cleared_twice(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
                CartCleared::occur($cartId)
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->clear();
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_be_cleared_if_is_already_empty(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->clear();
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_can_change_product_quantity(): void
    {
        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->changeProductQuantity('1', 10);
            })
            ->then([
                CartItemQuantityChanged::occur(
                    $cartId,
                    ProductCode::fromString('1'),
                    CartItemQuantity::create(2),
                    CartItemQuantity::create(10)
                )
            ])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_change_product_quantity_if_item_does_not_exist(): void
    {
        $this->expectException(CartItemNotFoundException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->changeProductQuantity('1', 10);
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_change_product_quantity_for_empty_product_code(): void
    {
        $this->expectException(ProductCodeCannotBeEmptyException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->changeProductQuantity('', 10);
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_change_product_quantity_if_new_quantity_is_zero(): void
    {
        $this->expectException(InvalidCartItemQuantityException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->changeProductQuantity('1', 0);
            })
            ->then([])
        ;
    }

    /**
     * @test
     */
    public function it_cannot_change_product_quantity_if_new_quantity_is_below_zero(): void
    {
        $this->expectException(InvalidCartItemQuantityException::class);

        $cartId = Uuid::uuid4();
        $cartCurrency = new Currency('USD');
        $this->scenario
            ->given([
                CartPickedUp::occur($cartId, $cartCurrency),
                CartItemAdded::occur(
                    $cartId,
                    CartItem::create(ProductCode::fromString('1'), CartItemQuantity::create(2), Money::USD(1000))
                ),
            ])
            ->when(function (CartContract $cart) use ($cartId, $cartCurrency) {
                $cart->changeProductQuantity('1', -10);
            })
            ->then([])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAggregateRootClass(): string
    {
        return Cart::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAggregateRootFactory(): CartFactory
    {
        return new CartAggregateFactory(new ISOCurrenciesProvider());
    }
}
