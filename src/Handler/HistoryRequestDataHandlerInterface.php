<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\HistoryRequestData;
use App\Model\HistoryResponseData;

interface HistoryRequestDataHandlerInterface
{
    public function handle(HistoryRequestData $request): HistoryResponseData;
}
