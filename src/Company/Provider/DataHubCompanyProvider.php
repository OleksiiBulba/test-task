<?php

declare(strict_types=1);

namespace App\Company\Provider;

use App\Company\Exception\CouldNotLoadCompaniesException;
use App\Company\Model\Company;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class DataHubCompanyProvider implements CompanyProviderInterface
{
    private const API_URL = 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';

    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getAllCompanies(): array
    {
        try {
            $response = $this->client->request(Request::METHOD_GET, self::API_URL);

            return $this->serializer->deserialize($response->getContent(), Company::class.'[]', JsonEncoder::FORMAT);
        } catch (\Throwable $e) {
            throw new CouldNotLoadCompaniesException(sprintf('Company list is not available: %s', $e->getMessage()), $e->getCode(), $e);
        }
    }

    public function searchBySymbol(string $symbol): ?Company
    {
        foreach ($this->getAllCompanies() as $company) {
            if ($symbol === $company->getSymbol()) {
                return $company;
            }
        }

        return null;
    }
}
