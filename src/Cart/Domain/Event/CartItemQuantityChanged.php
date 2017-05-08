<?php

declare(strict_types=1);

namespace Cart\Domain\Event;

use Broadway\Serializer\Serializable;
use Cart\Domain\Model\CartItemQuantity;
use Cart\Domain\Model\ProductCode;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class CartItemQuantityChanged implements Serializable
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @var ProductCode
     */
    private $productCode;

    /**
     * @var CartItemQuantity
     */
    private $oldCartItemQuantity;

    /**
     * @var CartItemQuantity
     */
    private $newCartItemQuantity;

    /**
     * @param UuidInterface $cartId
     * @param ProductCode $productCode
     * @param CartItemQuantity $oldCartItemQuantity
     * @param CartItemQuantity $newCartItemQuantity
     */
    private function __construct(
        UuidInterface $cartId,
        ProductCode $productCode,
        CartItemQuantity $oldCartItemQuantity,
        CartItemQuantity $newCartItemQuantity
    ) {
        $this->cartId = $cartId;
        $this->productCode = $productCode;
        $this->oldCartItemQuantity = $oldCartItemQuantity;
        $this->newCartItemQuantity = $newCartItemQuantity;
    }

    /**
     * @param UuidInterface $cartId
     * @param ProductCode $productCode
     * @param CartItemQuantity $oldCartItemQuantity
     * @param CartItemQuantity $newCartItemQuantity
     *
     * @return CartItemQuantityChanged
     */
    public static function occur(
        UuidInterface $cartId,
        ProductCode $productCode,
        CartItemQuantity $oldCartItemQuantity,
        CartItemQuantity $newCartItemQuantity
    ): self {
        return new self($cartId, $productCode, $oldCartItemQuantity, $newCartItemQuantity);
    }

    /**
     * @return UuidInterface
     */
    public function cartId(): UuidInterface
    {
        return $this->cartId;
    }

    /**
     * @return ProductCode
     */
    public function productCode(): ProductCode
    {
        return $this->productCode;
    }

    /**
     * @return CartItemQuantity
     */
    public function oldCartItemQuantity(): CartItemQuantity
    {
        return $this->oldCartItemQuantity;
    }

    /**
     * @return CartItemQuantity
     */
    public function newCartItemQuantity(): CartItemQuantity
    {
        return $this->newCartItemQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data): self
    {
        return new self(
            Uuid::fromString($data['cartId']),
            ProductCode::fromString($data['productCode']),
            CartItemQuantity::create($data['oldCartItemQuantity']),
            CartItemQuantity::create($data['newCartItemQuantity'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): array
    {
        return [
            'cartId' => (string) $this->cartId,
            'productCode' => (string) $this->productCode,
            'oldCartItemQuantity' => $this->oldCartItemQuantity->getNumber(),
            'newCartItemQuantity' => $this->newCartItemQuantity->getNumber(),
        ];
    }
}
