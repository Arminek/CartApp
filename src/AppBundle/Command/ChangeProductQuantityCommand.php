<?php

namespace AppBundle\Command;

use Broadway\CommandHandling\CommandBus;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Cart\Application\Command\ChangeProductQuantity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ChangeProductQuantityCommand extends Command
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    /**
     * @param CommandBus $commandBus
     * @param UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(CommandBus $commandBus, UuidGeneratorInterface $uuidGenerator)
    {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cart:change-product-quantity')
            ->setDescription('Change product quantity')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $cartId = $helper->ask(
            $input,
            $output,
            new Question(
                sprintf('Cart id (%s): ', $this->uuidGenerator->generate()),
                $this->uuidGenerator->generate()
            )
        );

        $productCode = $helper->ask($input, $output, new Question('Product code: '));
        $quantity = $helper->ask($input, $output, new Question('New quantity: ', 1));

        $changeCartItemQuantity = ChangeProductQuantity::create(Uuid::fromString($cartId), $productCode, $quantity);

        $this->commandBus->dispatch($changeCartItemQuantity);

        $output->writeln(sprintf('Quantity changed!'));
    }
}
