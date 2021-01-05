<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('publish')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function ($category) {
                    return $category->getTitle();
                }
              ])
            ->add('videos', CollectionType::class, [
                'entry_type'    => VideoFormType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'label' => false,
            ])  
            ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
