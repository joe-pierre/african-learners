<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Choisir une photo de couverture',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer la photo ?',
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'squared_thumbnail_small',
            ])
            ->add('courseFile', VichFileType::class, [
                'label' => 'Choisir le document pdf du cours',
                'required' => true,
                'allow_delete' => false,
                'download_uri' => false,
                'asset_helper' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choisir le document du cours'
                    ])
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre du cours',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Ajouter une description au cours',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}