<?php

declare(strict_types = 1);

namespace Cart\Application\CommandHandler;

use Broadway\CommandHandling\SimpleCommandHandler;
use Cart\Application\Repository\CartRepository;
use Cart\Application\Command\AddProductToCart;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class AddProductToCartCommandHandler extends SimpleCommandHandler
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
     * @param AddProductToCart $command
     */
    public function handleAddProductToCart(AddProductToCart $command): void
    {
        $cart = $this->cartRepository->load($command->cartId());

        $cart->addProductToCart($command->productCode(), $command->quantity(), $command->price(), $command->productCurrencyCode());

        $this->cartRepository->save($cart);
    }
}
