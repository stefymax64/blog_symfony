<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\FieldProvider;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

//    Configuration des éléments du CRUD
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Publication')
            ->setEntityLabelInPlural('Les publications')
            ->setPageTitle(crud::PAGE_NEW, "Ajouter une publication")
            ->setPageTitle(crud::PAGE_EDIT, "Modifier une publication");
    }

//    Lister les champs de manière ordonnée dans la page d'édition d'une publication
    public function configureFields(string $pageName): iterable
    {
        return
//            Récupèrer les tags et les associer à la publication
            yield AssociationField::new('tags');
            yield TextField::new('title', "Titre");
            yield TextField::new('slug', "Lien");
            yield TextField::new('summary', "Résumé");
//            Masquer le champ texte sur la page index
            yield TextareaField::new('content', "Contenu")->hideOnIndex();
    }

}
