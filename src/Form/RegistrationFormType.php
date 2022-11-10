<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => ''
                ],
                'expanded' => true,
                'choices' => [
                    'Candidat' => 'candidate',
                    'Recruteur' => 'recruiter',
                ],
                'multiple' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez cocher une case'])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un Email valide !']),
                    new Email(['mode' => 'html5', 'message' => 'L\'adresse {{ value }} n\'est pas valide']),
                ]
            ])
            // comme quoi user est ok qu on lui prenne ses data perso pour son inscription
            ->add('rgpdConsent', CheckboxType::class, [
                
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'invalid_message' => 'Le mot de passe et la confirmation doit être le même ',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez saisir un mot de passe!']),
                        new Length(['min' => 8, 'max' => 4096, 'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères']),
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez saisir le même mot de passe!']),
                        new Length(['min' => 6, 'max' => 100, 'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',]),
                    ]
                ],
            ]);
            // ->add('plainPassword', PasswordType::class, [
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'mapped' => false,
            //     'attr' => ['autocomplete' => 'new-password'],
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Please enter a password',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Your password should be at least {{ limit }} characters',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //         ]),
            //     ],
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}