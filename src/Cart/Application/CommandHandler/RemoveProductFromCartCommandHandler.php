<?php

declare(strict_types = 1);

namespace Cart\Application\CommandHandler;

use Broadway\CommandHandling\SimpleCommandHandler;
use Cart\Application\Command\RemoveProductFromCart;
use Cart\Application\Repository\CartRepository;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class RemoveProductFromCartCommandHandler extends SimpleCommandHandler
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
     * @param RemoveProductFromCart $command
     */
    public function handleRemoveProductFromCart(RemoveProductFromCart $command): void
    {
        $cart = $this->cartRepository->load($command->cartId());

        $cart->removeProductFromCart($command->productCode());

        $this->cartRepository->save($cart);
    }
}
