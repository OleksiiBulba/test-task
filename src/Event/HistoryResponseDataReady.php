<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\HistoryRequestData;
use App\Model\HistoryResponseData;
use Symfony\Contracts\EventDispatcher\Event;

class HistoryResponseDataReady extends Event
{
    public function __construct(
        private readonly HistoryRequestData $request,
        private readonly HistoryResponseData $response
    ) {
    }

    public function getRequest(): HistoryRequestData
    {
        return $this->request;
    }

    public function getResponse(): HistoryResponseData
    {
        return $this->response;
    }
}
