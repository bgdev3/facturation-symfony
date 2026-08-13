<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\LigneDevis;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('tauxTVA', TextType::class, [
                'label' => 'Taux TVA'
            ])
            ->add('devis', EntityType::class, [
                'class' => Devis::class,
                'label' => 'Devis en relation',
                'choice_label' => 'id',
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
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
