<?php

declare(strict_types=1);

namespace App\Company\Provider;

use App\Company\Exception\CouldNotLoadCompaniesException;
use App\Company\Model\Company;

interface CompanyProviderInterface
{
    /**
     * @return Company[]
     *
     * @throws CouldNotLoadCompaniesException
     */
    public function getAllCompanies(): array;

    public function searchBySymbol(string $symbol): ?Company;
}
