<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use Vich\UploaderBundle\Form\Type\VichFileType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setAutofocusSearch();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('createdAt')->add('user');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('imageFile', 'Photo de couverture du cours')->setFormType(VichImageType::class),
            ImageField::new('imageFile')->setBasePath('%app.image_uploads_path%')->onlyOnIndex(),
            TextField::new('courseFile', 'Fichier (PDF) du cours')
                        ->setFormType(VichFileType::class)
                        ->setFormTypeOptions([
                            'constraints' => [
                                new NotBlank(['message' => 'Choisir un fichier (PDF) pour ce cours'])
                            ]
                        ]),
            TextField::new('title', 'Titre')->hideOnIndex(),
            SlugField::new('slug')->setTargetFieldName('title'),
            TextareaField::new('description')->hideOnIndex(),
            AssociationField::new('user', 'Auteur du cours')->autocomplete(),
        ];
    }
}