<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Form\Image\ImageType;
use App\Form\Video\VideoEditType;
use App\Form\Video\VideoNewType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('category')
            ->add('content')
            //edit existing images
            ->add("images", CollectionType::class,[
                'entry_type' => ImageType::class,
                "required" => false,
            ])
            ->add("new_images", FileType::class,[
                "mapped" => false,
                "multiple" => true,
                "required" => false,
            ])
            //edit existing videos
            ->add("videos", CollectionType::class,[
                'entry_type' => VideoEditType::class,
                "required" => false
            ])
            ->add("new_videos", CollectionType::class,[
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
