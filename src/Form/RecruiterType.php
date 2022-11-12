<?php

namespace App\Form;

use App\Entity\Recruiter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecruiterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameCompany', TextType::class, [
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom d\'entreprise.']),
                    new Length(['min' => 5, 'max' => 100, 'minMessage' => 'le nom de la société doit contenir au moins {{ limit }} caractères']),
                    ]
            ])
            ->add('address', TextType::class, [
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse.']),
                    new Length(['min' => 5, 'max' => 250, 'minMessage' => 'Votre adresse doit contenir au moins {{ limit }} caractères']),
                    ]
            ])
            ->add('zipCode', TextType::class, [
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un code postal.']),
                    new Length(['min' => 5, 'max' => 10, 'minMessage' => 'Votre code postal doit contenir au moins {{ limit }} caractères']),
                    ]
            ])
            ->add('city', TextType::class, [
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une ville.']),
                    new Length(['min' => 8, 'max' => 250, 'minMessage' => 'Le nom de votre ville doit contenir au moins {{ limit }} caractères']),
                    ]
            ])
            // ->add('active')
            // ->add('user', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recruiter::class,
        ]);
    }
}