<?php

declare(strict_types=1);

namespace App\HistoricalQuotes\Fetcher;

use App\Model\HistoricalQuotes\Collection;

interface HistoricalQuotesByDateRangeFetcherInterface
{
    public function fetchData(string $symbol, \DateTimeInterface $from, \DateTimeInterface $to, string $region = ''): Collection;
}
