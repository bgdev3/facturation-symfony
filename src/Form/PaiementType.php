<?php

namespace App\Form;

use App\Entity\Paiement;
use App\Enum\PaiementStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('montant', MoneyType::class, [
            'label' => 'Montant (euros)',
            'currency' => false,
        ])
            ->add('date', DateType::class, ['widget' => 'single_text'])
            ->add('moyen', EnumType::class, [
                'class' => PaiementStatus::class, 
                'label' => 'Règlement',
                 'attr' => [
                        'class' => 'w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white appearance-none focus:outline-none focus:ring-2 focus:ring-primary-blue',
                    ]
                
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'inline-block w-50 mx-auto px-2.5 py-1.5 text-center bg-green-300 rounded-md text-dark text-xs hover:bg-green-100 transition-colors mx-auto'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
