<?php

namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre nom.']),
                    new Length(['min' => 3, 'max' => 100, 'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères']),
                    ]
            ])
            ->add('lastname', TextType::class, [
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre prénom.']),
                    new Length(['min' => 3, 'max' => 100, 'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères']),
                    ]
            ])
            // ->add('cv', TextType::class, [
            //     'label' => 'CV',
            //     'required' => false,
            //     'attr' => [
            //         'maxlength' => 100
            //     ]
            // ])
            ->add('cv', FileType::class, [
                'label' => 'CV (fichier PDF - taille maximale : 1024 Ko)',
                'attr' => [
                    'maxlength' => 100
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un document au format PDF.',
                    ])
                ],
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
        ]);
    }
}