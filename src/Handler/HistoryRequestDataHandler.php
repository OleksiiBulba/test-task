<?php

declare(strict_types=1);

namespace App\Handler;

use App\Event\HistoryResponseDataReady;
use App\Model\HistoryRequestData;
use App\Model\HistoryResponseData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class HistoryRequestDataHandler implements HistoryRequestDataHandlerInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function handle(HistoryRequestData $request): HistoryResponseData
    {
        // Step 1: Fetch history data from an external API.
        $response = $this->fetchHistoryData($request);

        // Step 2: Dispatch event that history data is fetched and ready.
        $this->dispatchHistoryDataReady($request, $response);

        // Step 3: Return an object with data.
        return $response;
    }

    private function fetchHistoryData(HistoryRequestData $request): HistoryResponseData
    {
        return new HistoryResponseData(); // TODO: implement fetching historical data
    }

    private function dispatchHistoryDataReady(HistoryRequestData $request, HistoryResponseData $response): void
    {
        $this->dispatcher->dispatch(new HistoryResponseDataReady($request, $response));
    }
}
