<?php

declare(strict_types=1);

namespace App\Tests\HistoricalQuotes\Fetcher;

use App\HistoricalQuotes\Exception\HistoricalQuotesNotFoundException;
use App\HistoricalQuotes\Fetcher\RapidHistoricalDataFetcher;
use App\HistoricalQuotes\Model\Collection as HistoricalQuotesCollection;
use App\HistoricalQuotes\Model\Price;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RapidHistoricalDataFetcherTest extends TestCase
{
    private RapidHistoricalDataFetcher $model;

    private HttpClientInterface&MockObject $httpClientMock;

    private SerializerInterface&MockObject $serializerMock;

    private ResponseInterface&Stub $responseStub;

    private string $apiKey = 'CdXbS0dQlSYMAKlg2m0u';

    protected function setUp(): void
    {
        $this->httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['request'])
            ->getMockForAbstractClass();

        $this->responseStub = $this->createStub(ResponseInterface::class);

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deserialize'])
            ->getMockForAbstractClass();

        $this->model = new RapidHistoricalDataFetcher(
            $this->httpClientMock,
            $this->serializerMock,
            $this->apiKey
        );
    }

    /**
     * @dataProvider fetchDataDataProvider
     */
    public function testFetchData(
        HistoricalQuotesCollection $expectedCollection,
        string $symbol,
        ?string $region,
        string $serializedHistoricalQuotesData,
        array $expectedQuery
    ): void {
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                Request::METHOD_GET,
                'https://'.RapidHistoricalDataFetcher::RAPID_API_HOST.RapidHistoricalDataFetcher::RAPID_API_ENDPOINT_HISTORICAL_DATA,
                [
                    'query' => $expectedQuery,
                    'headers' => [
                        RapidHistoricalDataFetcher::RAPID_HEADER_API_KEY => $this->apiKey,
                        RapidHistoricalDataFetcher::RAPID_HEADER_API_HOST => RapidHistoricalDataFetcher::RAPID_API_HOST,
                    ],
                ]
            )->willReturn($this->responseStub);

        $this->responseStub->method('getContent')
            ->willReturn($serializedHistoricalQuotesData);

        $this->serializerMock->expects($this->once())
            ->method('deserialize')
            ->with($serializedHistoricalQuotesData, HistoricalQuotesCollection::class, JsonEncoder::FORMAT)
            ->willReturn($expectedCollection);

        $this->assertEquals($expectedCollection, $this->model->fetchData($symbol, $region));
    }

    public static function fetchDataDataProvider():array
    {
        $collection = (new HistoricalQuotesCollection())
            ->setPrices([
                (new Price())
                    ->setDate(1682083800)
                    ->setOpen(165.0500030517578)
                    ->setHigh(166.4499969482422)
                    ->setLow(164.49000549316406)
                    ->setClose(165.02000427246094)
                    ->setVolume(58311900),
                (new Price())
                    ->setDate(1681997400)
                    ->setOpen(166.08999633789062)
                    ->setHigh(167.8699951171875)
                    ->setLow(165.55999755859375)
                    ->setClose(166.64999389648438)
                    ->setVolume(52456400),
            ]);

        $serialized = <<<QUOTES
            {
                "prices": [
                    {
                        "date": 1682083800,
                        "open": 165.0500030517578,
                        "high": 166.4499969482422,
                        "low": 164.49000549316406,
                        "close": 165.02000427246094,
                        "volume": 58311900,
                        "adjclose": 165.02000427246094
                    },
                    {
                        "date": 1681997400,
                        "open": 166.08999633789062,
                        "high": 167.8699951171875,
                        "low": 165.55999755859375,
                        "close": 166.64999389648438,
                        "volume": 52456400,
                        "adjclose": 166.64999389648438
                    },
                    {
                        "amount": 0.23,
                        "date": 1676039400,
                        "type": "DIVIDEND",
                        "data": 0.23
                    }
                ],
                "isPending": false,
                "firstTradeDate": 345479400,
                "id": "",
                "timeZone": {
                    "gmtOffset": -14400
                },
                "eventsData": [
                     {
                        "amount": 0.23,
                        "date": 1676039400,
                        "type": "DIVIDEND",
                        "data": 0.23
                    }
                ]
            }
            QUOTES;


        return [
            [
                'expectedCollection' => $collection,
                'symbol' => 'AAPL',
                'region' => null,
                'serializedHistoricalQuotesData' => $serialized,
                'expectedQuery' => [
                    'symbol' => 'AAPL',
                ],
            ],
            [
                'expectedCollection' => $collection,
                'symbol' => 'AAPL',
                'region' => 'US',
                'serializedHistoricalQuotesData' => $serialized,
                'expectedQuery' => [
                    'symbol' => 'AAPL',
                    'region' => 'US',
                ],
            ]
        ];
    }

    public function testFetchDataWithTransportException(): void
    {
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                Request::METHOD_GET,
                'https://'.RapidHistoricalDataFetcher::RAPID_API_HOST.RapidHistoricalDataFetcher::RAPID_API_ENDPOINT_HISTORICAL_DATA,
                [
                    'query' => ['symbol' => 'AAPL'],
                    'headers' => [
                        RapidHistoricalDataFetcher::RAPID_HEADER_API_KEY => $this->apiKey,
                        RapidHistoricalDataFetcher::RAPID_HEADER_API_HOST => RapidHistoricalDataFetcher::RAPID_API_HOST,
                    ],
                ]
            )->willThrowException(new TransportException('Some transport exception test message.'));

        $this->expectException(\RuntimeException::class);

        $this->model->fetchData('AAPL');
    }

    /**
     * @dataProvider fetchDataWithHistoricalQuotesNotFoundExceptionDataProvider
     */
    public function testFetchDataWithHistoricalQuotesNotFoundException(
        string $symbol,
        ?string $region,
        string $serializedHistoricalQuotesData,
        array $expectedQuery
    ): void {
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                Request::METHOD_GET,
                'https://'.RapidHistoricalDataFetcher::RAPID_API_HOST.RapidHistoricalDataFetcher::RAPID_API_ENDPOINT_HISTORICAL_DATA,
                [
                    'query' => $expectedQuery,
                    'headers' => [
                        RapidHistoricalDataFetcher::RAPID_HEADER_API_KEY => $this->apiKey,
                        RapidHistoricalDataFetcher::RAPID_HEADER_API_HOST => RapidHistoricalDataFetcher::RAPID_API_HOST,
                    ],
                ]
            )->willReturn($this->responseStub);

        $this->responseStub->method('getContent')
            ->willReturn($serializedHistoricalQuotesData);

        $this->serializerMock->expects($this->once())
            ->method('deserialize')
            ->with($serializedHistoricalQuotesData, HistoricalQuotesCollection::class, JsonEncoder::FORMAT)
            ->willThrowException(new \Exception('Some exception'));

        $this->expectException(HistoricalQuotesNotFoundException::class);
        $this->expectExceptionMessage('Some exception');

        $this->model->fetchData($symbol, $region);
    }

    public static function fetchDataWithHistoricalQuotesNotFoundExceptionDataProvider():array
    {
        $serialized = <<<QUOTES
            {
                "prices": [
                    {
                        "date": 1682083800,
                        "open": 165.0500030517578,
                        "high": 166.4499969482422,
                        "low": 164.49000549316406,
                        "close": 165.02000427246094,
                        "volume": 58311900,
                        "adjclose": 165.02000427246094
                    },
                    {
                        "date": 1681997400,
                        "open": 166.08999633789062,
                        "high": 167.8699951171875,
                        "low": 165.55999755859375,
                        "close": 166.64999389648438,
                        "volume": 52456400,
                        "adjclose": 166.64999389648438
                    },
                    {
                        "amount": 0.23,
                        "date": 1676039400,
                        "type": "DIVIDEND",
                        "data": 0.23
                    }
                ],
                "isPending": false,
                "firstTradeDate": 345479400,
                "id": "",
                "timeZone": {
                    "gmtOffset": -14400
                },
                "eventsData": [
                     {
                        "amount": 0.23,
                        "date": 1676039400,
                        "type": "DIVIDEND",
                        "data": 0.23
                    }
                ]
            }
            QUOTES;

        return [
            [
                'symbol' => 'AAPL',
                'region' => null,
                'serializedHistoricalQuotesData' => $serialized,
                'expectedQuery' => [
                    'symbol' => 'AAPL',
                ],
            ],
            [
                'symbol' => 'AAPL',
                'region' => 'US',
                'serializedHistoricalQuotesData' => $serialized,
                'expectedQuery' => [
                    'symbol' => 'AAPL',
                    'region' => 'US',
                ],
            ]
        ];
    }
}
