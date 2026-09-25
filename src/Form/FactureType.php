<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Enum\ConditionsStatus;
use App\Enum\FactureStatut;
use App\Repository\ClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function __construct(private readonly Security $security) 
    {
       
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        
        $builder
            ->add('numero', TextType::class, [
                'label' => 'N° de facture', 
                'disabled' => true,
            ])

            ->add('dateEmission', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'émission'
            ])

            ->add('dateEcheance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\échéance'
            ])

            ->add('statut', EnumType::class, [
                'class' => FactureStatut::class, 
                'label' => 'Statut',
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])

            ->add('client', EntityType::class, [
                'class' => Client::class,
                  'query_builder' => function (ClientRepository $repo) use ($user) {
                    return $repo->createQueryBuilder('c')->where('c.user = :user')->setParameter('user', $user);
                },
                'label' => 'Client en relation',
                'choice_label' => 'name',
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])

             ->add('devis', EntityType::class, [
                'class' => Devis::class,
               'placeholder' => 'Sélectionnez un devis',
                'label' => 'Devis en relation',
                'required' => false,
                'choice_label' => 'numero',
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])

            ->add('montantHT', TextType::class, [
                   'mapped' => false,
                   'required' => false
            ])

            ->add('montantTVA',  TextType::class, [  
                'mapped' => false,
                'required' => false
                ])

            ->add('montantTTC',  TextType::class, [
                'mapped' => false,
                'required' => false,
            ])

            ->add('conditionsPaiement', EnumType::class, [
                'class' => ConditionsStatus::class,
                'label' => 'Conditions de paiement', 
                'attr' => ['class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500']
            ])

            ->add('ligneFactures', CollectionType::class, [
                'entry_type' => LigneFactureType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
              ->add('save', SubmitType::class,[
                'label' => 'Sauvegarder',
                'attr' => ['class' => 'inline-block px-2.5 py-1.5 text-center bg-green-300 font-bold rounded-md text-dark text-xs hover:bg-green-100 transition-colors'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
            'csrf_token_id' => 'facture_form',
        ]);
    }
}
