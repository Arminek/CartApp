<?php

declare(strict_types = 1);

namespace Cart\Application\Adapter\AvailableCurrencies;

use Cart\Domain\Model\AvailableCurrenciesProvider;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ISOCurrenciesProvider implements AvailableCurrenciesProvider
{
    /**
     * @var ISOCurrencies
     */
    private $isoCurrencies;

    public function __construct()
    {
        $this->isoCurrencies = new ISOCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function provideAvailableCurrencies(): Currencies
    {
        return $this->isoCurrencies;
    }
}
