<?php

declare(strict_types = 1);

namespace Cart\Application\Command;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class RemoveProductFromCart
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
     * @param UuidInterface $cartId
     * @param string $productCode
     */
    private function __construct(UuidInterface $cartId, string $productCode)
    {
        $this->cartId = $cartId;
        $this->productCode = $productCode;
    }

    /**
     * @param UuidInterface $cartId
     * @param string $productCode
     *
     * @return self
     */
    public static function create(UuidInterface $cartId, string $productCode): self
    {
        return new self($cartId, $productCode);
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
}
