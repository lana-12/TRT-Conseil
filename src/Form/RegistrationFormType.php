<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
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
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => ''
                ],
                'expanded' => true,
                'choices' => [
                    'Candidat' => 'ROLE_CANDIDATE',
                    'Recruteur' => 'ROLE_RECRUITER',
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
            // ->add('rgpdConsent', CheckboxType::class, [
                
            //     'mapped' => false,
            //     'constraints' => [
            //         new IsTrue([
            //             'message' => 'You should agree to our terms.',
            //         ]),
            //     ],
            // ])
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
        // Permet la manipulation an array
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    // transform the array to a string
                    return implode(', ', $rolesAsArray);
                },
                function ($rolesAsString) {
                    // transform the string back to an array
                    return explode(', ', $rolesAsString);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}