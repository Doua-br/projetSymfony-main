<?php

namespace App\Form;

use App\Entity\Administrateur;
use App\Entity\Coach;
use App\Entity\Evenement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('image', FileType::class, [
                'label' => 'Votre image de profil (Des fichiers images uniquement)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('description')
            ->add('lieu')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            
            ->add('coach', EntityType::class, [
                'class' => Coach::class,
                'choice_label' => 'id',
            ])
            ->add('administrateur', EntityType::class, [
                'class' => Administrateur::class,
                'choice_label' => 'id',
            ])
            ->add('Ok', SubmitType::class)
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }


}
