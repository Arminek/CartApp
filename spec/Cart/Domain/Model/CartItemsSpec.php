<?php

declare(strict_types=1);

namespace spec\Cart\Domain\Model;

use Cart\Domain\Model\CartItem;
use Cart\Domain\Model\CartItemCollection;
use Cart\Domain\Model\CartItemQuantity;
use Cart\Domain\Model\CartItems;
use Cart\Domain\Model\ProductCode;
use Cart\Domain\Exception\CartItemNotFoundException;
use Money\Money;
use PhpSpec\ObjectBehavior;

final class CartItemsSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('createEmpty');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CartItems::class);
    }

    function it_is_cart_items_model_collection(): void
    {
        $this->shouldImplement(CartItemCollection::class);
    }

    function it_can_add_new_cart_item(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));

        $this->add($cartItem);
        $this->exists($cartItem)->shouldReturn(true);
    }

    function it_can_merge_existing_cart_items(): void
    {
        $mugItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $sameMugItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(4), Money::USD(15));

        $this->add($mugItem);
        $this->add($sameMugItem);

        $this->findOneByProductCode(ProductCode::fromString('Mug'))->shouldBeLike(
            CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(6), Money::USD(15))
        );
    }

    function it_can_remove_existing_cart_item(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $this->beConstructedThrough('fromArray', [[(string) $cartItem->productCode() => $cartItem]]);

        $this->remove($cartItem);
        $this->exists($cartItem)->shouldReturn(false);
    }

    function it_throws_cart_item_not_found_exception_if_cart_item_does_not_exist(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));

        $this->shouldThrow(CartItemNotFoundException::class)->during('remove', [$cartItem]);
    }

    function it_can_find_cart_items_by_product_code(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $this->beConstructedThrough('fromArray', [[(string) $cartItem->productCode() => $cartItem]]);

        $this->findOneByProductCode(ProductCode::fromString('Mug'))->shouldReturn($cartItem);
    }

    function it_can_find_all_cart_items(): void
    {
        $mugCartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $bookCartItem = CartItem::create(ProductCode::fromString('Book'), CartItemQuantity::create(20), Money::USD(100));
        $this->add($mugCartItem);
        $this->add($bookCartItem);

        $this->findAll()->shouldReturn(
            [(string) $mugCartItem->productCode() => $mugCartItem, (string) $bookCartItem->productCode() => $bookCartItem]
        );
    }

    function it_can_find_one_by_id(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $this->beConstructedThrough('fromArray', [[(string) $cartItem->productCode() => $cartItem]]);

        $this->findOneByProductCode($cartItem->productCode())->shouldReturn($cartItem);
    }

    function it_throws_cart_item_not_found_exception_if_cannot_find_cart_item_by_product_code(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $this->beConstructedThrough('fromArray', [[(string) $cartItem->productCode() => $cartItem]]);

        $this->shouldThrow(CartItemNotFoundException::class)->during('findOneByProductCode', [ProductCode::fromString('Book')]);
    }

    function it_can_clear_cart_items(): void
    {
        $mugCartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $bookCartItem = CartItem::create(ProductCode::fromString('Book'), CartItemQuantity::create(20), Money::USD(100));
        $this->add($mugCartItem);
        $this->add($bookCartItem);

        $this->clear();

        $this->findAll()->shouldReturn([]);
    }

    function it_can_count_items(): void
    {
        $mugCartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $bookCartItem = CartItem::create(ProductCode::fromString('Book'), CartItemQuantity::create(20), Money::USD(100));
        $this->add($mugCartItem);
        $this->add($bookCartItem);

        $this->count()->shouldReturn(2);
    }

    function it_knows_if_is_actually_empty_or_not(): void
    {
        $this->isEmpty()->shouldReturn(true);
    }

    function it_is_not_empty_if_is_initialized_with_cart_items(): void
    {
        $cartItem = CartItem::create(ProductCode::fromString('Mug'), CartItemQuantity::create(2), Money::USD(10));
        $this->beConstructedThrough('fromArray', [[(string) $cartItem->productCode() => $cartItem]]);
        $this->isEmpty()->shouldReturn(false);
    }
}
