<?php

declare(strict_types=1);

namespace Cart\Infrastructure\Repository;

use Broadway\Repository\AggregateNotFoundException;
use Broadway\Repository\Repository;
use Cart\Application\Repository\CartRepository;
use Cart\Domain\Model\CartContract;
use Cart\Infrastructure\Exception\CartNotFoundException;
use Ramsey\Uuid\UuidInterface;

final class BroadwayCartRepository implements CartRepository
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function load(UuidInterface $cartId): CartContract
    {
        try {
            /** @var CartContract $cart */
            $cart = $this->repository->load($cartId);

            return $cart;
        } catch (AggregateNotFoundException $exception) {
            throw CartNotFoundException::withId($cartId, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(CartContract $cart): void
    {
        $this->repository->save($cart);
    }
}
