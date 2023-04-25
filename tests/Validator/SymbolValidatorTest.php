<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Company\Model\Company;
use App\Company\Provider\CompanyProviderInterface;
use App\Validator\Symbol;
use App\Validator\SymbolValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @implements SymbolValidator
 */
class SymbolValidatorTest extends ConstraintValidatorTestCase
{
    private CompanyProviderInterface&MockObject $companyProviderMock;

    protected function setUp(): void
    {
        $this->companyProviderMock = $this->getMockBuilder(CompanyProviderInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAllCompanies', 'searchBySymbol'])
            ->getMockForAbstractClass();

        parent::setUp();
    }

    protected function createValidator(): SymbolValidator
    {
        return new SymbolValidator($this->companyProviderMock);
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Symbol());

        $this->assertNoViolation();
    }

    public function testBlankIsValid(): void
    {
        $this->validator->validate('', new Symbol());

        $this->assertNoViolation();
    }

    public function testValidSymbol(): void
    {
        $this->companyProviderMock->expects($this->never())
            ->method('getAllCompanies');

        $this->companyProviderMock->expects($this->any())
            ->method('searchBySymbol')
            ->with('AAA')
            ->willReturn(
                (new Company())
                    ->setName('AAA Company')
                    ->setSymbol('AAA')
            );

        $this->validator->validate('AAA', new Symbol());

        $this->assertNoViolation();
    }

    public function testInvalidSymbol(): void
    {
        $this->companyProviderMock->expects($this->never())
            ->method('getAllCompanies');

        $this->companyProviderMock->expects($this->any())
            ->method('searchBySymbol')
            ->with('AAA')
            ->willReturn(null);

        $this->validator->validate('AAA', new Symbol());

        $this->buildViolation('Unknown company symbol "{{ value }}"')
            ->setParameter('{{ value }}', 'AAA')
            ->setCode(Symbol::NO_SUCH_SYMBOL_ERROR)
            ->setInvalidValue('AAA')
            ->assertRaised()
        ;
    }

    public function testIncorrectConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('AAA', new Blank());
    }
}
