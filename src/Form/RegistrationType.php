<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class,[
                'expanded' => true,
                'choices'=>[
                    'Candidat'=>'candidate',
                    'Recruteur' => 'recruiter',
                ],
                'multiple'=> false,
                'required'=> true,
                'constraints'=>[
                    new NotBlank(['message'=> 'Veuillez cocher une case'])
                ]
            ] )
            
            ->add('email', EmailType::class,[
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un Email valide !']),
                    new Email(['mode' => 'html5', 'message' => 'L\'adresse {{ value }} n\'est pas valide']),
                ]
            ])
            
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped'=>false,
                'invalid_message' => 'Le mot de passe et la confirmation doit être le même ',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe!']),
                    new Length(['min' => 8, 'max' => 100, 'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères']),
                    ]
                ],
                'second_options' => [
                    'label'=> 'Confirmer le mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir le même mot de passe!']),
                    new Length(['min' => 6, 'max' => 100, 'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',]),
                    ]
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}