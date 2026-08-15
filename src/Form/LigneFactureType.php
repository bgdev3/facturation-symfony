<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\LigneFacture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => ' Désignation'
            ])
            ->add('quantite', TextType::class, [
                'label' => ' Quantité'
            ])
            ->add('prixUnitaireHT', TextType::class, [
                'label' => ' Prix unitaire HT'
            ])
            ->add('tauxTVA', TextType::class, [
                'label' => ' Taux TVA'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneFacture::class,
        ]);
    }
}
