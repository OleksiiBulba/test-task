<?php

declare(strict_types=1);

namespace App\Company\Provider;

use App\Company\Model\Company;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class DataHubCompanyProvider implements CompanyProviderInterface
{
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
        $response = $this->client->request(Request::METHOD_GET, 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json');

        return $this->serializer->deserialize($response->getContent(), Company::class.'[]', JsonEncoder::FORMAT);
    }
}
