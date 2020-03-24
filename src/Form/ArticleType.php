<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            // Champ de type text pour le titre de l'article
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'attr' => ['class' => 'form-control mb-4'],
                'empty_data' => ''
            ])
            // Champ de type text utilisant le bundle fosckeditor (inclus dans 'public/bundles') pour le contenu de l'article
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
            // Champ de type File pour l'image de l'article
            ->add('image', FileType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control-file'],
                'empty_data' => ''
            ])
            //Bouton de submit
            ->add('submit', SubmitType::class, [
                'label' => 'Créer l\'article',
                'attr' => ['class' => 'btn btn-outline-primary btn-lg']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class
        ]);
    }
}
