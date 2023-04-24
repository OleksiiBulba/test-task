<?php

declare(strict_types=1);

namespace App\Company\Provider;

class DataHubCompanyProvider implements CompanyProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllCompanies(): array
    {
        return [];
    }
}
