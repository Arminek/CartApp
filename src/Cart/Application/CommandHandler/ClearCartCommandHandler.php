<?php

declare(strict_types = 1);

namespace Cart\Application\CommandHandler;

use Broadway\CommandHandling\SimpleCommandHandler;
use Cart\Application\Command\ClearCart;
use Cart\Application\Repository\CartRepository;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ClearCartCommandHandler extends SimpleCommandHandler
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @param CartRepository $cartRepository
     */
    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param ClearCart $command
     */
    public function handleClearCart(ClearCart $command): void
    {
        $cart = $this->cartRepository->load($command->cartId());

        $cart->clear();

        $this->cartRepository->save($cart);
    }
}
