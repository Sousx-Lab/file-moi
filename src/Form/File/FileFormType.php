<?php
namespace App\Form\File;

use App\Entity\File\Data\FileData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', FileType::class, [
                'multiple' => true,
                'attr' => [ 
                    'multiple' => true, 
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileData::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
