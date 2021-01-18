<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('url', TypeTextType::class,[
            'label' => false,
            'attr' => ['placeholder' => 'Saisir un lien embed. ex : https://www.youtube.com/embed/........'],
            'constraints' => [
                new Regex([
                    'pattern' => '/embed/',
                    'match' => 'false',
                    'message' => 'Le lien doit contenir "embed"'
                ])
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
