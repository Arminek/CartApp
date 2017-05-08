<?php

declare(strict_types=1);

namespace Cart\Infrastructure\Producer;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

final class CartPublisher implements EventListener
{
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage): void
    {
        $message = [
            'playhead' => $domainMessage->getPlayhead(),
            'type' => $this->getEventName($domainMessage->getPayload()),
            'payload' => $domainMessage->getPayload()->serialize(),
            'recordedOn' => $domainMessage->getRecordedOn()->toString(),
        ];

        $this->producer->publish($this->serialize($message));
    }

    /**
     * @param object $event
     *
     * @return string
     */
    private function getEventName($event): string
    {
        $classParts = explode('\\', get_class($event));

        return end($classParts);
    }

    /**
     * @param array $message
     *
     * @return string
     */
    private function serialize(array $message): string
    {
        return json_encode($message);
    }
}
