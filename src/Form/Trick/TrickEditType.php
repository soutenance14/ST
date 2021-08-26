<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Form\Image\ImageType;
use App\Form\Video\VideoEditType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add("images", CollectionType::class,[
                'entry_type' => ImageType::class,
                // "mapped" => false,
                // "multiple" => true,
                "required" => false,
            ])
            ->add("new_images", FileType::class,[
                "mapped" => false,
                "multiple" => true,
                "required" => false,
            ])
            ->add("videos", CollectionType::class,[
                'entry_type' => VideoEditType::class,
                // "mapped" => false,
                // "multiple" => true,
                "required" => false,
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
