<?php

declare(strict_types = 1);

namespace Cart\Domain\Event;

use Broadway\Serializer\Serializable;
use Money\Currency;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class CartPickedUp implements Serializable
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @var Currency
     */
    private $cartCurrency;

    /**
     * @param UuidInterface $cartId
     * @param Currency $cartCurrency
     */
    private function __construct(UuidInterface $cartId, Currency $cartCurrency)
    {
        $this->cartId = $cartId;
        $this->cartCurrency = $cartCurrency;
    }

    /**
     * @param UuidInterface $cartId
     * @param Currency $cartCurrency
     *
     * @return self
     */
    public static function occur(UuidInterface $cartId, Currency $cartCurrency): self
    {
        return new self($cartId, $cartCurrency);
    }

    /**
     * @return UuidInterface
     */
    public function cartId(): UuidInterface
    {
        return $this->cartId;
    }

    /**
     * @return Currency
     */
    public function cartCurrency(): Currency
    {
        return $this->cartCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['cartId']),
            new Currency($data['cartCurrency'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return [
            'cartId' => $this->cartId->toString(),
            'cartCurrency' => $this->cartCurrency->jsonSerialize()
        ];
    }
}
