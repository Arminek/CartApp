<?php

declare(strict_types = 1);

namespace Cart\Application\Command;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ChangeProductQuantity
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
    private $newQuantity;

    /**
     * @param UuidInterface $cartId
     * @param string $productCode
     * @param int $newQuantity
     */
    private function __construct(UuidInterface $cartId, string $productCode, int $newQuantity)
    {
        $this->cartId = $cartId;
        $this->productCode = $productCode;
        $this->newQuantity = $newQuantity;
    }

    /**
     * @param UuidInterface $cartId
     * @param string $productCode
     * @param int $newQuantity
     *
     * @return self
     */
    public static function create(UuidInterface $cartId, string $productCode, int $newQuantity): self
    {
        return new self($cartId, $productCode, $newQuantity);
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
    public function newQuantity(): int
    {
        return $this->newQuantity;
    }
}
