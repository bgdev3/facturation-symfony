<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Devis;
use App\Enum\DevisStatut;
use App\Form\LigneDevisType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, [
                'label' => 'N° de devis'
            ])
            ->add('dateEmission', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'émission'
                
            ])
            ->add('dateValidite', DateType::class, [
                'widget' => 'single_text',
                 'label' => 'Date de validité'
            ])
            ->add('statut', EnumType::class, [
                'label' => 'Statut',
                'class'=> DevisStatut::class,
                 'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])
            
            ->add('montantHT', TextType::class,[
                'label' => 'Montant HT'
            ])
            ->add('montantTVA', TextType::class,[
                'label' => 'Montant TVA'
            ])
            ->add('montantTTC', TextType::class,[
                'label' => 'Montant TTC'
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Client en relation',
                'choice_label' => 'id',
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])
            ->add('ligneDevis', CollectionType::class, [
                'label' => 'Ligne de Devis',
                'entry_type' => LigneDevisType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
