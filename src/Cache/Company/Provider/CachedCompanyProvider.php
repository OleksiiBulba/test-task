<?php

declare(strict_types=1);

namespace App\Cache\Company\Provider;

use App\Company\Model\Company;
use App\Company\Provider\CompanyProviderInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

readonly class CachedCompanyProvider implements CompanyProviderInterface
{
    private const CACHE_KEY = 'CachedCompanyProvider-getAllCompanies';

    private const EXPIRES_AFTER = 3600; // 1 hour

    public function __construct(private CompanyProviderInterface $companyProvider, private CacheItemPoolInterface $cacheItemPool)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAllCompanies(): array
    {
        return $this->getCachedCompanies();
    }

    public function searchBySymbol(string $symbol): ?Company
    {
        $companies = $this->getCachedCompanies();

        return $companies[$symbol] ?? null;
    }

    /**
     * @return Company[]
     */
    private function getCachedCompanies(): array
    {
        try {
            $item = $this->cacheItemPool->getItem(self::CACHE_KEY);
            // @codeCoverageIgnoreStart
        } catch (InvalidArgumentException) {
            return $this->getIndexedCompanies();
            // @codeCoverageIgnoreEnd
        }

        if ($item->isHit()) {
            return $item->get();
        }

        $companies = $this->getIndexedCompanies();
        $item->set($companies);
        $item->expiresAfter(self::EXPIRES_AFTER);
        $this->cacheItemPool->saveDeferred($item);

        return $companies;
    }

    /**
     * @return Company[]
     */
    private function getIndexedCompanies(): array
    {
        $companies = [];
        foreach ($this->companyProvider->getAllCompanies() as $company) {
            $companies[$company->getSymbol()] = $company;
        }

        return $companies;
    }
}
