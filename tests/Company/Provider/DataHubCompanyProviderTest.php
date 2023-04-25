<?php

declare(strict_types=1);

namespace App\Tests\Company\Provider;

use App\Company\Exception\CouldNotLoadCompaniesException;
use App\Company\Model\Company;
use App\Company\Provider\DataHubCompanyProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DataHubCompanyProviderTest extends TestCase
{
    private DataHubCompanyProvider $model;

    private HttpClientInterface&MockObject $httpClientMock;

    private SerializerInterface&MockObject $serializerMock;

    private ResponseInterface&Stub $responseStub;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['request'])
            ->getMockForAbstractClass();

        $this->responseStub = $this->createStub(ResponseInterface::class);

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deserialize'])
            ->getMockForAbstractClass();

        $this->model = new DataHubCompanyProvider(
            $this->httpClientMock,
            $this->serializerMock
        );
    }

    /**
     * @param Company[] $expectedCompaniesList
     *
     * @dataProvider getAllCompaniesDataProvider
     */
    public function testGetAllCompanies(array $expectedCompaniesList, string $serializedCompaniesList): void
    {
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(Request::METHOD_GET, DataHubCompanyProvider::API_URL)
            ->willReturn($this->responseStub);

        $this->responseStub->method('getContent')
            ->willReturn($serializedCompaniesList);

        $this->serializerMock->expects($this->once())
            ->method('deserialize')
            ->with($serializedCompaniesList, Company::class.'[]', JsonEncoder::FORMAT)
            ->willReturn($expectedCompaniesList);

        $this->assertEquals($expectedCompaniesList, $this->model->getAllCompanies());
    }

    public function testCompaniesAreNotAvailable(): void
    {
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('Not available'));

        $this->expectException(CouldNotLoadCompaniesException::class);
        $this->expectExceptionMessage('Company list is not available: Not available');

        $this->model->getAllCompanies();
    }

    public static function getAllCompaniesDataProvider(): array
    {
        $companiesData = [
            'OB' => 'Oleksii Bulba Ltd',
            'GOOG' => 'Google',
        ];

        return [
            [
                'expectedCompaniesList' => [...self::createCompanies($companiesData)],
                'serializedCompaniesList' => self::createSerializedCompaniesList($companiesData),
            ],
        ];
    }

    /**
     * @param Company[] $expectedCompaniesList
     *
     * @dataProvider searchBySymbolDataProvider
     */
    public function testSearchBySymbol(
        ?Company $expectedCompany,
        string $symbol,
        array $expectedCompaniesList,
        string $serializedCompaniesList
    ): void {
        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(Request::METHOD_GET, DataHubCompanyProvider::API_URL)
            ->willReturn($this->responseStub);

        $this->responseStub->method('getContent')
            ->willReturn($serializedCompaniesList);

        $this->serializerMock->expects($this->once())
            ->method('deserialize')
            ->with($serializedCompaniesList, Company::class.'[]', JsonEncoder::FORMAT)
            ->willReturn($expectedCompaniesList);

        $this->assertEquals($expectedCompany, $this->model->searchBySymbol($symbol));
    }

    public static function searchBySymbolDataProvider(): array
    {
        $company = (new Company())->setName('AAA Company')->setSymbol('AAA');

        return [
            [
                'expectedCompany' => $company,
                'symbol' => 'AAA',
                'expectedCompaniesList' => [$company],
                'serializedCompaniesList' => '[{"Company Name":"AAA Company","Symbol":"AAA"}]',
            ],
            [
                'expectedCompany' => null,
                'symbol' => 'BBB',
                'expectedCompaniesList' => [$company],
                'serializedCompaniesList' => '[{"Company Name":"AAA Company","Symbol":"AAA"}]',
            ],
        ];
    }

    /**
     * @param array<string, string> $companiesData
     *
     * @return Company[]
     */
    private static function createCompanies(array $companiesData): array
    {
        $companies = [];
        foreach ($companiesData as $symbol => $name) {
            $companies[] = (new Company())
                ->setName($name)
                ->setSymbol($symbol);
        }

        return $companies;
    }

    /**
     * @param array<string, string> $companiesData
     */
    private static function createSerializedCompaniesList(array $companiesData): string
    {
        $list = [];
        foreach ($companiesData as $symbol => $name) {
            $list[] = sprintf('{"Company Name":"%s","Symbol":"%s"}', $name, $symbol);
        }

        return sprintf('[%s]', implode(',', $list));
    }
}
