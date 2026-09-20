<?php

namespace App\Form;

use App\Entity\LigneDevis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneDevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Désignation'
            ])
            ->add('quantite', TextType::class, [
                'label' => 'Quantité'
            ])
            ->add('prixUnitaireHT', TextType::class, [
                'label' => 'Prix unitaire HT'
            ])
            ->add('tauxTVA', HiddenType::class, [
                'empty_data' => '20.00',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneDevis::class,
        ]);
    }
}
