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

    const EXPIRES_AFTER = 3600; // 1 hour

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
        } catch (InvalidArgumentException) {
            /** @codeCoverageIgnore */
            return $this->doFetchData($symbol, $region);
        }

        if ($item->isHit()) {
            return $item->get();
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
        $cacheKey = self::CACHE_KEY_PREFIX . $symbol;
        if (null !== $region) {
            $cacheKey .= '-' . $region;
        }

        return $cacheKey;
    }
}