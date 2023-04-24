<?php

namespace App\Form;

use App\Model\HistoryDataRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoryDataFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $symbolChoices = $options['symbol_choices'] ?? [];
        $builder
            ->add('symbol', ChoiceType::class, [
                'required' => true,
                'choices' => array_flip($symbolChoices),
                'placeholder' => 0 < \count($symbolChoices) ? '' : 'Could not load symbol choices',
            ])
            ->add('startDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => HistoryDataRequest::class])
            ->setRequired(['symbol_choices']);
    }
}
