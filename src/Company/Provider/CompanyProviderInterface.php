<?php

declare(strict_types=1);

namespace App\Company\Provider;

use App\Company\Exception\CouldNotLoadCompaniesException;
use App\Company\Model\Company;

interface CompanyProviderInterface
{
    /**
     * @throws CouldNotLoadCompaniesException
     *
     * @return Company[]
     */
    public function getAllCompanies(): array;
}
