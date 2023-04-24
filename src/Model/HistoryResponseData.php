<?php

declare(strict_types=1);

namespace App\Model;

use App\Company\Model\Company;

class HistoryResponseData
{
    private Company $company;

    public function getData(): string
    {
        return 'dummy response';
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
