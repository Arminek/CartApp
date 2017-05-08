<?php

declare(strict_types = 1);

namespace Cart\Application\CommandHandler;

use Broadway\CommandHandling\SimpleCommandHandler;
use Cart\Application\Command\PickUpCart;
use Cart\Application\Factory\CartFactory;
use Cart\Application\Repository\CartRepository;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class PickUpCartCommandHandler extends SimpleCommandHandler
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @var CartFactory
     */
    private $cartFactory;

    /**
     * @param CartRepository $cartRepository
     * @param CartFactory $cartFactory
     */
    public function __construct(CartRepository $cartRepository, CartFactory $cartFactory)
    {
        $this->cartRepository = $cartRepository;
        $this->cartFactory = $cartFactory;
    }

    /**
     * @param PickUpCart $command
     */
    public function handlePickUpCart(PickUpCart $command): void
    {
        $cart = $this->cartFactory->pickUp($command->cartId(), $command->currencyCode());

        $this->cartRepository->save($cart);
    }
}
