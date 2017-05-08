<?php

declare(strict_types = 1);

namespace Cart\Application\Command;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class PickUpCart
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @param UuidInterface $cartId
     * @param string $currencyCode
     */
    private function __construct(UuidInterface $cartId, string $currencyCode)
    {
        $this->cartId = $cartId;
        $this->currencyCode = $currencyCode;
    }

    /**
     * @param UuidInterface $cartId
     * @param string $currencyCode
     *
     * @return PickUpCart
     */
    public static function create(UuidInterface $cartId, string $currencyCode): self
    {
        return new self($cartId, $currencyCode);
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
    public function currencyCode(): string
    {
        return $this->currencyCode;
    }
}
