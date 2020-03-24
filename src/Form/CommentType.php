<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Champ de type Textarea pour le commentaire
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => ['class' => 'form-control mb-2'],
                'empty_data' => ''
            ])
            // Bouton de submit
            ->add('submit', SubmitType::class, [
                'label' => 'Poster le commentaire',
                'attr' => ['class' => 'btn btn-outline-light mb-5']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
