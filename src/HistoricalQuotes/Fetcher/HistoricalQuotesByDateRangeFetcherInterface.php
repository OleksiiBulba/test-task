<?php

declare(strict_types=1);

namespace App\HistoricalQuotes\Fetcher;

use App\HistoricalQuotes\Model\Collection;

interface HistoricalQuotesByDateRangeFetcherInterface
{
    public function fetchData(string $symbol, \DateTimeInterface $from, \DateTimeInterface $to, string $region = ''): Collection;
}
