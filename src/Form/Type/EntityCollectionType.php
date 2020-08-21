<?php

namespace App\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityCollectionType extends AbstractType
{
    private ManagerRegistry $registry;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor)
    {
        $this->registry = $registry;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new CallbackTransformer(
            fn (?array $entities = null) => array_map(fn ($entity) => $this->propertyAccessor->getValue(
                $entity,
                $options['identifier_property']
            ), $entities ?? []),
            fn ($identifiers)=> $identifiers ? $this->registry->getRepository($options['class'])->findBy(
                [$options['identifier_property'] => $identifiers]
            ) : [],
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => TextType::class,
            'allow_add' => true,
        ]);

        $resolver->define('class')
            ->required()
            ->allowedTypes('string');

        $resolver->define('identifier_property')
            ->required()
            ->allowedTypes('string');
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}
