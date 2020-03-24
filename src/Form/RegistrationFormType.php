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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On construit le formulaire d'inscription en ajoutant directement les contraintes, notamment por gérer la répétion de mot de passe
        $builder
            // Champ de type Text pour le pseudo
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => ['class' => 'form-control mb-2'],
                'empty_data' => '',
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre pseudo doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre pseudo ne peut pas faire plus de {{ limit }} caractères'
                    ]),
                ],
            ])
            // Champ de type Email pour l'email (on vérifie qu'on a bien un email)
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'empty_data' => '',
                'attr' => ['class' => 'form-control mb-2'],
                'constraints' => [
                    new Email(['message' => 'Votre adresse email est invalide']),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Merci d\'entrer une adresse email',
                        'max' => 255,
                        'maxMessage' => 'Votre email ne peut pas faire plus de {{ limit }} caractères'
                    ])
                ]
            ])
            // Checkbox pou accepter les conditions d'utilisation
            ->add('agreeTerms', CheckboxType::class, [
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation',
                    ]),
                ],
            ])
            // Champ de type Repeated avec deux champs Password pour gérer la répétition du mot de passe
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Vos mots de passe ne correspondent pas',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control  mb-2']
                ],
                'second_options' => [
                    'label' => 'Répétez votre mot de passe',
                    'attr' => ['class' => 'form-control mb-4']
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
