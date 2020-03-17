<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Nouveau pseudo',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre pseudo doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre pseudo ne peut pas faire plus de {{ limit }} caractères'
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Nouveau email',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Email(['message' => 'Votre adresse email est invalide'])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Vos mots de passe ne correspondent pas',
                'required' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control  mb-2']
                ],
                'second_options' => [
                    'label' => 'Répétez votre mot de passe',
                    'attr' => ['class' => 'form-control mb-4', 'placeholder' => 'Répétez votre mot de passe']
                ],
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre mot de passe ne peut pas faire plus de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('avatar', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,
                'attr' => ['class' => 'form-control-file']
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
