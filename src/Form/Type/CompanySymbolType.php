<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Company\Provider\CompanyProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanySymbolType extends AbstractType
{
    public function __construct(private readonly CompanyProviderInterface $companyProvider)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $companies = $this->companyProvider->getAllCompanies();
        $symbolChoices = [];

        foreach ($companies as $company) {
            $symbolChoices[(string) $company] = $company->getSymbol();
        }

        $resolver->setDefaults([
            'choices' => $symbolChoices,
            'placeholder' => 0 < \count($symbolChoices) ? '' : 'Could not load symbol choices',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
