<?php

declare(strict_types=1);

namespace App\Model\HistoricalQuotes;

class Collection
{
    /**
     * @var Price[]
     */
    private array $prices;

    /**
     * @return Price[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param Price[] $prices
     */
    public function setPrices(array $prices): self
    {
        $this->prices = $prices;

        return $this;
    }
}
