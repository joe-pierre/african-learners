<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la vidéo',
                ])
            ->add('video_url', TextType::class, [
                'label' => 'URL de la vidéo',
                'attr' => [
                    'placeholder' => 'exemple : https://www.youtube.com/watch?v=',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Déscription',
                'attr' => [
                    'placeholder' => 'Ajouter une description à la vidéo',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}