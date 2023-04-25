<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HistoricalDataControllerTest extends WebTestCase
{
    private KernelBrowser $kernelBrowser;

    private function getHttpClientMock(): HttpClientInterface
    {
        return new TraceableHttpClient(new MockHttpClient(function (string $method, string $url) {
            return match (true) {
                str_contains($url, 'nasdaq-listings') => new MockResponse(<<<COMPANY
                    [
                        {
                            "Company Name": "Apple Inc.",
                            "Financial Status": "N",
                            "Market Category": "Q",
                            "Round Lot Size": 100.0,
                            "Security Name": "Apple Inc. - Common Stock",
                            "Symbol": "AAPL",
                            "Test Issue": "N"
                        },
                        {
                            "Company Name": "Avalanche Biotechnologies, Inc.",
                            "Financial Status": "N",
                            "Market Category": "G",
                            "Round Lot Size": 100.0,
                            "Security Name": "Avalanche Biotechnologies, Inc. - Common Stock",
                            "Symbol": "AAVL",
                            "Test Issue": "N"
                        }
                    ]
                    COMPANY,
                    ['http_code' => 200]
                ),
                str_contains($url, 'rapidapi.com') => new MockResponse(<<<HISTORICAL
                    {
                        "prices": [
                            {
                                "date": 1682083800,
                                "open": 165.0500030517578,
                                "high": 166.4499969482422,
                                "low": 164.49000549316406,
                                "close": 165.02000427246094,
                                "volume": 58311900,
                                "adjclose": 165.02000427246094
                            },
                            {
                                "date": 1681997400,
                                "open": 166.08999633789062,
                                "high": 167.8699951171875,
                                "low": 165.55999755859375,
                                "close": 166.64999389648438,
                                "volume": 52456400,
                                "adjclose": 166.64999389648438
                            },
                            {
                                "date": 1681911000,
                                "open": 165.8000030517578,
                                "high": 168.16000366210938,
                                "low": 165.5399932861328,
                                "close": 167.6300048828125,
                                "volume": 47720200,
                                "adjclose": 167.6300048828125
                            },
                            {
                                "date": 1681824600,
                                "open": 166.10000610351562,
                                "high": 167.41000366210938,
                                "low": 165.64999389648438,
                                "close": 166.47000122070312,
                                "volume": 49923000,
                                "adjclose": 166.47000122070312
                            },
                            {
                                "amount": 0.23,
                                "date": 1676039400,
                                "type": "DIVIDEND",
                                "data": 0.23
                            },
                            {
                                "date": 1675953000,
                                "open": 153.77999877929688,
                                "high": 154.3300018310547,
                                "low": 150.4199981689453,
                                "close": 150.8699951171875,
                                "volume": 56007100,
                                "adjclose": 150.63999938964844
                            }
                        ],
                        "isPending": false,
                        "firstTradeDate": 345479400,
                        "id": "",
                        "timeZone": {
                            "gmtOffset": -14400
                        },
                        "eventsData": [
                             {
                                "amount": 0.23,
                                "date": 1676039400,
                                "type": "DIVIDEND",
                                "data": 0.23
                            }
                        ]
                    }
                    HISTORICAL,
                    ['http_code' => 200]
                ),
                default => null,
            };
        }));
    }

    protected function setUp(): void
    {
        $this->kernelBrowser = self::createClient();
        $this->kernelBrowser->getContainer()->set('test.'.HttpClientInterface::class, $this->getHttpClientMock());
        $this->kernelBrowser->followRedirects();
        $this->kernelBrowser->disableReboot();
    }

    public function testIndex(): void
    {
        $this->kernelBrowser->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
    }

    public function testEmptySubmit(): void
    {
        $this->kernelBrowser->request(Request::METHOD_GET, '/');
        $this->kernelBrowser->submitForm('Submit');
        $this->assertStringContainsStringIgnoringCase(
            'This value should not be blank.',
            $this->kernelBrowser->getResponse()->getContent()
        );
    }

    public function testValidSubmit(): void
    {
        $this->kernelBrowser->request(Request::METHOD_GET, '/');

        $this->kernelBrowser->submitForm('Submit', ['history_data_form' => [
            'symbol' => 'AAPL',
            'startDate' => '2023-04-19',
            'endDate' => '2023-04-20',
            'email' => 'oleksii_bulba@epam.com',
        ]]);

        $this->assertStringNotContainsString('alert-danger', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-24</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-23</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-22</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-21</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-20</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringContainsString('<td>2023-04-19</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-18</td>', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('<td>2023-04-17</td>', $this->kernelBrowser->getResponse()->getContent());

        $this->assertStringNotContainsString('58311900', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('52456400', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringContainsString('47720200', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('49923000', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringNotContainsString('56007100', $this->kernelBrowser->getResponse()->getContent());
    }

    public function testDangerFlash(): void
    {
        $this->kernelBrowser->request(Request::METHOD_GET, '/');

        $this->kernelBrowser->getContainer()->set('test.'.MailerInterface::class, new class() implements MailerInterface {
            public function send(RawMessage $message, Envelope $envelope = null): void
            {
                throw new TransportException('Cannot send email');
            }
        });

        $this->kernelBrowser->submitForm('Submit', ['history_data_form' => [
            'symbol' => 'AAPL',
            'startDate' => '2023-04-19',
            'endDate' => '2023-04-20',
            'email' => 'oleksii_bulba@epam.com',
        ]]);

        $this->assertStringContainsStringIgnoringCase('alert-danger', $this->kernelBrowser->getResponse()->getContent());
        $this->assertStringContainsStringIgnoringCase('Could not send an email notification', $this->kernelBrowser->getResponse()->getContent());
    }
}
