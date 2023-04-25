<?php

declare(strict_types=1);

namespace App\Cache\HistoricalQuotes\Fetcher;

use App\HistoricalQuotes\Fetcher\HistoricalQuotesFetcherInterface;
use App\HistoricalQuotes\Model\Collection;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

readonly class CachedHistoricalQuotesFetcher implements HistoricalQuotesFetcherInterface
{
    private const CACHE_KEY_PREFIX = 'CachedHistoricalQuotesFetcherDecorator-fetchData-';

    private const EXPIRES_AFTER = 3600; // 1 hour

    public function __construct(
        private HistoricalQuotesFetcherInterface $historicalQuotesFetcher,
        private CacheItemPoolInterface $cacheItemPool
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchData(string $symbol, string $region = null): Collection
    {
        try {
            $item = $this->cacheItemPool->getItem($this->getCacheKey($symbol, $region));
            // @codeCoverageIgnoreStart
        } catch (InvalidArgumentException) {
            return $this->doFetchData($symbol, $region);
            // @codeCoverageIgnoreEnd
        }

        if ($item->isHit()) {
            // @codeCoverageIgnoreStart
            return $item->get();
            // @codeCoverageIgnoreEnd
        }

        $data = $this->doFetchData($symbol, $region);
        $item->set($data);
        $item->expiresAfter(self::EXPIRES_AFTER);
        $this->cacheItemPool->saveDeferred($item);

        return $data;
    }

    private function doFetchData(string $symbol, ?string $region = null): Collection
    {
        return $this->historicalQuotesFetcher->fetchData($symbol, $region);
    }

    private function getCacheKey(string $symbol, ?string $region): string
    {
        $cacheKey = self::CACHE_KEY_PREFIX.$symbol;
        if (null !== $region) {
            // @codeCoverageIgnoreStart
            $cacheKey .= '-'.$region;
            // @codeCoverageIgnoreEnd
        }

        return $cacheKey;
    }
}
