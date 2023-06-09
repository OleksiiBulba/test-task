<?php

declare(strict_types=1);

namespace App\HistoricalQuotes\Fetcher;

use App\HistoricalQuotes\Exception\HistoricalQuotesNotFoundException;
use App\HistoricalQuotes\Model\Collection;
use App\HistoricalQuotes\Model\Price;

readonly class HistoricalQuotesByDateRangeFetcher implements HistoricalQuotesByDateRangeFetcherInterface
{
    public function __construct(private HistoricalQuotesFetcherInterface $historicalQuotesFetcher)
    {
    }

    /**
     * @throws HistoricalQuotesNotFoundException
     */
    public function fetchData(string $symbol, \DateTimeInterface $from, \DateTimeInterface $to, string $region = null): Collection
    {
        $historicalQuotesCollection = $this->historicalQuotesFetcher->fetchData($symbol, $region);

        $prices = $historicalQuotesCollection->getPrices();
        $prices = array_filter($prices, $this->getFilterByRange($from, $to));

        $historicalQuotesCollection->setPrices($prices);

        return $historicalQuotesCollection;
    }

    private function getFilterByRange(\DateTimeInterface $from, \DateTimeInterface $to): callable
    {
        return function (Price $price) use ($from, $to) {
            return $price->getDate() > $from && $price->getDate() < $to;
        };
    }
}
