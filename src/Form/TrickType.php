<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                        new Regex([
                            'pattern' => '/[a-zA-z]{3,}[a-zA-Z-0-9\-]*/',
                            'message' => 'Le titre doit commencer par 3 lettres'
                        ])]
                ])  
            ->add('description')
            ->add('publish')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function ($category) {
                    return $category->getTitle();
                }
              ])
            ->add('images', CollectionType::class, [
                'entry_type'    => ImageFormType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
            ])  
            ->add('videos', CollectionType::class, [
                'entry_type'    => VideoFormType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
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
