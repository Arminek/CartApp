<?php

declare(strict_types = 1);

namespace Cart\Domain\Event;

use Broadway\Serializer\Serializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class CartCleared implements Serializable
{
    /**
     * @var UuidInterface
     */
    private $cartId;

    /**
     * @param UuidInterface $cartId
     */
    private function __construct(UuidInterface $cartId)
    {
        $this->cartId = $cartId;
    }

    /**
     * @param UuidInterface $cartId
     *
     * @return CartCleared
     */
    public static function occur(UuidInterface $cartId): self
    {
        return new self($cartId);
    }

    /**
     * @return UuidInterface
     */
    public function cartId(): UuidInterface
    {
        return $this->cartId;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['cartId'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return [
            'cartId' => $this->cartId->toString()
        ];
    }
}
