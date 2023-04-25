<?php

declare(strict_types=1);

namespace App\Handler;

use App\Event\HistoryRequestFinished;
use App\HistoricalQuotes\Fetcher\HistoricalQuotesByDateRangeFetcherInterface;
use App\Model\HistoricalDataRequest;
use App\Model\HistoricalDataResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class HistoryRequestDataHandler implements HistoryRequestDataHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private HistoricalQuotesByDateRangeFetcherInterface $quotesByDateRangeFetcher
    ) {
    }

    public function handle(HistoricalDataRequest $request): HistoricalDataResponse
    {
        // Step 1: Fetch history data from an external API.
        $data = $this->quotesByDateRangeFetcher->fetchData(
            $request->getSymbol(),
            $request->getStartDate(),
            $request->getEndDate()
        );

        $response = (new HistoricalDataResponse())
            ->setHistoricalQuotesCollection($data);

        // Step 2: Dispatch event that history data is fetched and ready.
        $this->dispatchHistoryDataReady($request);

        // Step 3: Return an object with data.
        return $response;
    }

    private function dispatchHistoryDataReady(HistoricalDataRequest $request): void
    {
        $this->dispatcher->dispatch(new HistoryRequestFinished($request));
    }
}
