<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\HistoricalDataRequest;
use Symfony\Contracts\EventDispatcher\Event;

/** @codeCoverageIgnore */
class HistoryRequestFinished extends Event
{
    public function __construct(private readonly HistoricalDataRequest $request)
    {
    }

    public function getRequest(): HistoricalDataRequest
    {
        return $this->request;
    }
}
