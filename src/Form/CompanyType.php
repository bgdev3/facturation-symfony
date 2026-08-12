<?php

namespace App\Form;

use App\Entity\Company;
use App\EventSubscriber\LogoSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CompanyType extends AbstractType
{
    public function __construct(private LogoSubscriber $logo_subscriber ) {}
  
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Raison sociale'
            ])
            ->add('siret', TextType::class, [
                'label' => 'Siret'
            ])
            ->add('address', TextType::class, [
                'label' => 'Siège social'
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal entreprise'
            ])
            ->add('city', TextType::class, [
                'label' => 'Résidence entreprise'
            ])
            ->add('tvaIntraCom', TextType::class, [
                'label' => 'TVA Intra communautaire',
                'required' => false
            ])
            ->add('iban', TextType::class, [
                'label' => 'IBAN'
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC'
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo',
                'required' => false,
                'mapped' => false,
                 'constraints' => [
                new File(
                    maxSize: '2M',
                    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                    mimeTypesMessage: 'Merci d\'uploader une image valide',
                    )
                ]
            ])
            ->addEventSubscriber($this->logo_subscriber);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
