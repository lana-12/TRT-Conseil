<?php

namespace App\Form;

use App\Entity\JobOffer;
use App\Entity\Recruiter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class JobOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        //     ->add('recruiter', EntityType::class, [
        //         'class'=> Recruiter::class,
        //         'attr' => [
        //             'class' => 'form-control',
        //         ],
        //         'required' => true,
        //         'query_builder'=> function(){
                    
        //         }
        // ])


        
            ->add('title', TextType::class, [
            'attr' => [
                'class' => 'form-control mb-3',
            ],
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez saisir un titre valide.']),
                new Length(['min' => 5, 'max' => 100, 'minMessage' => 'Le titre de la société doit contenir au moins {{ limit }} caractères']),
            ]
        ])
            ->add('city', TextType::class, [
            'attr' => [
                'class' => 'form-control mb-3',
            ],
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez saisir un nom de ville valide.']),
                new Length(['min' => 5, 'max' => 100, 'minMessage' => 'Le titre de la société doit contenir au moins {{ limit }} caractères']),
            ]
        ])
            ->add('content', TextareaType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'Veuillez saisir une description valide.']),
            ]
        ])
            // ->add('Recruiter')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobOffer::class,
        ]);
    }
}