<?php

declare(strict_types = 1);

namespace Cart\Application\CommandHandler;

use Broadway\CommandHandling\SimpleCommandHandler;
use Cart\Application\Repository\CartRepository;
use Cart\Application\Command\ChangeProductQuantity;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ChangeProductQuantityCommandHandler extends SimpleCommandHandler
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
     * @param ChangeProductQuantity $command
     */
    public function handleChangeProductQuantity(ChangeProductQuantity $command): void
    {
        $cart = $this->cartRepository->load($command->cartId());

        $cart->changeProductQuantity($command->productCode(), $command->getNewQuantity());

        $this->cartRepository->save($cart);
    }
}
