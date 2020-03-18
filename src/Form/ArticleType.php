<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'attr' => ['class' => 'form-control mb-4'],
                'empty_data' => ''
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu de l\'article',
                'attr' => ['class' => 'form-control'],
                'empty_data' => '',
                'config' => [
                    'toolbar' => 'basic',
                    'language' => 'fr',
                    'uiColor' => '#FCFCFC'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control-file']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer l\'article',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
