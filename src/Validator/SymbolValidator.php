<?php

declare(strict_types=1);

namespace App\Validator;

use App\Company\Provider\CompanyProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SymbolValidator extends ConstraintValidator
{
    public function __construct(private readonly CompanyProviderInterface $companyProvider)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Symbol) {
            throw new UnexpectedTypeException($constraint, Symbol::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $company = $this->companyProvider->searchBySymbol($value);
        if (null !== $company) {
            return;
        }

        $this->context->buildViolation($constraint->unknownSymbolMessage)
            ->setParameter('{{ value }}', $value)
            ->setCode(Symbol::NO_SUCH_SYMBOL_ERROR)
            ->setInvalidValue($value)
            ->addViolation()
        ;
    }
}