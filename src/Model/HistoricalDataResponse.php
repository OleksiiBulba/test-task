<?php

declare(strict_types=1);

namespace App\Model;

use App\HistoricalQuotes\Model\Collection as HistoricalQuotesCollection;

/** @codeCoverageIgnore */
class HistoricalDataResponse
{
    private HistoricalQuotesCollection $historicalQuotesCollection;

    public function getHistoricalQuotesCollection(): HistoricalQuotesCollection
    {
        return $this->historicalQuotesCollection;
    }

    public function setHistoricalQuotesCollection(HistoricalQuotesCollection $historicalQuotesCollection): self
    {
        $this->historicalQuotesCollection = $historicalQuotesCollection;

        return $this;
    }
}
