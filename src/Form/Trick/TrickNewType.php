<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Form\Video\VideoNewType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category')
            ->add('content')
            ->add("images", FileType::class,[
                "mapped" => false,
                "multiple" => true,
                "required" => false,
            ])
            ->add("videos", CollectionType::class,[
                'entry_type' => VideoNewType::class,
                "mapped" => false,
                "required" => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
