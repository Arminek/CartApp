<?php

declare(strict_types = 1);

namespace Cart\Application\Command;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class AddProductToCart
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @var string
     */
    private $productCode;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $price;

    /**
     * @var string
     */
    private $productCurrencyCode;

    /**
     * @param UuidInterface $cartId
     * @param string $productCode
     * @param int $quantity
     * @param int $price
     * @param string $productCurrencyCode
     */
    private function __construct(
        UuidInterface $cartId,
        string $productCode,
        int $quantity,
        int $price,
        string $productCurrencyCode
    ) {
        $this->cartId = $cartId;
        $this->productCode = $productCode;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->productCurrencyCode = $productCurrencyCode;
    }

    /**
     * @param UuidInterface $cartId
     * @param string $productCode
     * @param int $quantity
     * @param int $price
     * @param string $productCurrencyCode
     *
     * @return self
     */
    public static function create(
        UuidInterface $cartId,
        string $productCode,
        int $quantity,
        int $price,
        string $productCurrencyCode
    ): self {
        return new self($cartId, $productCode, $quantity, $price, $productCurrencyCode);
    }

    /**
     * @return UuidInterface
     */
    public function cartId(): UuidInterface
    {
        return $this->cartId;
    }

    /**
     * @return string
     */
    public function productCode(): string
    {
        return $this->productCode;
    }

    /**
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function price(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function productCurrencyCode(): string
    {
        return $this->productCurrencyCode;
    }
}
