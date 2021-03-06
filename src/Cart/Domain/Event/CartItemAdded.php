<?php

declare(strict_types = 1);

namespace Cart\Domain\Event;

use Broadway\Serializer\Serializable;
use Cart\Domain\Model\CartItem;
use Cart\Domain\Model\CartItemQuantity;
use Cart\Domain\Model\ProductCode;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class CartItemAdded implements Serializable
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @var CartItem
     */
    private $cartItem;

    /**
     * @param UuidInterface $cartId
     * @param CartItem $cartItem
     */
    private function __construct(UuidInterface $cartId, CartItem $cartItem)
    {
        $this->cartId = $cartId;
        $this->cartItem = $cartItem;
    }

    /**
     * @param UuidInterface $cartId
     * @param CartItem $cartItem
     *
     * @return CartItemAdded
     */
    public static function occur(UuidInterface $cartId, CartItem $cartItem): self
    {
        return new self($cartId, $cartItem);
    }

    /**
     * @return UuidInterface
     */
    public function cartId(): UuidInterface
    {
        return $this->cartId;
    }

    /**
     * @return CartItem
     */
    public function cartItem(): CartItem
    {
        return $this->cartItem;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['cartId']),
            new CartItem(
                ProductCode::fromString($data['cartItem']['productCode']),
                CartItemQuantity::create($data['cartItem']['quantity']),
                new Money($data['cartItem']['unitPrice']['amount'], new Currency($data['cartItem']['unitPrice']['currency']))
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return [
            'cartId' => $this->cartId->toString(),
            'cartItem' => [
                'productCode' => $this->cartItem->productCode()->__toString(),
                'quantity' => $this->cartItem->quantity()->getNumber(),
                'unitPrice' => $this->cartItem->unitPrice()->jsonSerialize(),
            ]
        ];
    }
}
