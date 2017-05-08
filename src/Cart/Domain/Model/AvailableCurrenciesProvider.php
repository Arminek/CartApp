<?php

declare(strict_types = 1);

namespace Cart\Domain\Model;

use Money\Currencies;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface AvailableCurrenciesProvider
{
    /**
     * @return Currencies
     */
    public function provideAvailableCurrencies(): Currencies;
}
