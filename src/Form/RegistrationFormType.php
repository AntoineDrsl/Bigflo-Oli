<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On construit le formulaire de connexion en ajoutant des contraintes
        $builder
            ->add('pseudo', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre pseudo doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre pseudo ne peut pas faire plus de {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les conditions d\'utilisation',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Vos mots de passe ne correspondent pas',
                'first_options' => [
                    'label' => false,
                    'attr' => ['class' => 'form-control']
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['class' => 'form-control mt-1 mb-4', 'placeholder' => 'Répétez votre mot de passe']
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre mot de passe ne peut pas faire plus de {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
