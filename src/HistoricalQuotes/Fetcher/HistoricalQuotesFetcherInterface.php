<?php

declare(strict_types=1);

namespace App\HistoricalQuotes\Fetcher;

use App\HistoricalQuotes\Exception\HistoricalQuotesNotFoundException;
use App\Model\HistoricalQuotes\Collection;

interface HistoricalQuotesFetcherInterface
{
    /**
     * @throws HistoricalQuotesNotFoundException
     */
    public function fetchData(string $symbol, string $region = ''): Collection;
}
