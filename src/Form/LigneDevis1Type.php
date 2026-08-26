<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\LigneDevis;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneDevis1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation')
            ->add('quantite')
            ->add('prixUnitaireHT')
            ->add('tauxTVA')
            ->add('montantHT')
            ->add('devis', EntityType::class, [
                'class' => Devis::class,
                'choice_label' => 'id',
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
