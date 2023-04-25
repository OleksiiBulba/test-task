<?php

declare(strict_types=1);

namespace App\HistoricalQuotes\Fetcher;

use App\HistoricalQuotes\Exception\HistoricalQuotesNotFoundException;
use App\HistoricalQuotes\Model\Collection;
use App\HistoricalQuotes\Model\Collection as HistoricalQuotesCollection;
use App\HistoricalQuotes\Model\Price;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class RapidHistoricalDataFetcher implements HistoricalQuotesFetcherInterface
{
    private const RAPID_API_HOST = 'yh-finance.p.rapidapi.com';
    private const RAPID_API_ENDPOINT_HISTORICAL_DATA = '/stock/v3/get-historical-data';
    private const RAPID_HEADER_API_KEY = 'X-RapidAPI-Key';
    private const RAPID_HEADER_API_HOST = 'X-RapidAPI-Host';

    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private string $rapidApiKey
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchData(string $symbol, string $region = null): Collection
    {
        $query = ['symbol' => $symbol];

        if (null !== $region) {
            $query['region'] = $region;
        }

        try {
            $response = $this->client->request(
                Request::METHOD_GET,
                'https://'.self::RAPID_API_HOST.self::RAPID_API_ENDPOINT_HISTORICAL_DATA,
                [
                    'query' => $query,
                    'headers' => [
                        self::RAPID_HEADER_API_KEY => $this->rapidApiKey,
                        self::RAPID_HEADER_API_HOST => self::RAPID_API_HOST,
                    ],
                ]
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            /**
             * TODO: we are loosing precising during json_decode:
             * Compare values:
             *      original 2.254300117492676 (15 digits)
             *       decoded 2.2543001174927   (13 digits).
             */
            /** @var HistoricalQuotesCollection $historyResponseData */
            $historyResponseData = $this->serializer->deserialize(
                $response->getContent(),
                HistoricalQuotesCollection::class,
                JsonEncoder::FORMAT
            );
        } catch (\Throwable $e) {
            throw new HistoricalQuotesNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        if (null === $historyResponseData) {
            throw new HistoricalQuotesNotFoundException();
        }

        $historyResponseData->setPrices(array_filter($historyResponseData->getPrices(), function (Price $price) {
            return '' === $price->getType();
        }));

        return $historyResponseData;
    }
}
