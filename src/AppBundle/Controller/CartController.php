<?php

declare(strict_types = 1);

namespace AppBundle\Controller;

use Broadway\CommandHandling\CommandBus;
use Cart\Application\Command\AddProductToCart;
use Cart\Application\Command\ChangeProductQuantity;
use Cart\Application\Command\ClearCart;
use Cart\Application\Command\PickUpCart;
use Cart\Application\Command\RemoveProductFromCart;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class CartController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function pickUpAction(Request $request): Response
    {
        $cartId = Uuid::uuid4();

        $content = json_decode($request->getContent(), true);
        $this->tryToHandleCommand(PickUpCart::create($cartId, $content['currencyCode']));

        return new Response($cartId);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function addProductAction(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $this->tryToHandleCommand(AddProductToCart::create(
            Uuid::fromString($content['cartId']),
            $content['productCode'],
            (int) $content['quantity'],
            (int) $content['price'],
            $content['productCurrencyCode']
        ));

        return new Response();
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function changeProductQuantityAction(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $this->tryToHandleCommand(ChangeProductQuantity::create(
            Uuid::fromString($content['cartId']),
            $content['productCode'],
            (int) $content['quantity']
        ));

        return new Response();
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function removeProductAction(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $this->tryToHandleCommand(RemoveProductFromCart::create(
            Uuid::fromString($content['cartId']),
            $content['productCode']
        ));

        return new Response();
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function clearAction(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $this->tryToHandleCommand(ClearCart::create(
            Uuid::fromString($content['cartId'])
        ));

        return new Response();
    }

    /**
     * @param $command
     *
     * @throws HttpException
     */
    private function tryToHandleCommand($command)
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (\DomainException $exception) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $exception->getMessage(), $exception);
        }
    }
}
