<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\HistoricalDataRequest;
use App\Model\HistoricalDataResponse;

interface HistoryRequestDataHandlerInterface
{
    public function handle(HistoricalDataRequest $request): HistoricalDataResponse;
}
