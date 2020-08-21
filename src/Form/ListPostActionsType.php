<?php

namespace App\Form;

use App\Entity\Post;
use App\Form\Type\EntityCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ListPostActionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('selectedPosts', EntityCollectionType::class, [
                'class' => Post::class,
                'identifier_property' => 'id',
            ])
            ->add('disable', SubmitType::class)
            ->add('remove', SubmitType::class)
        ;
    }
}
