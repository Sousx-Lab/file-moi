<?php

namespace App\Form\File;

use App\Entity\File\Data\FileData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FileFormType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', FileType::class, [
                'multiple' => true,
                'constraints' => new All(
                    [new File(
                        [],
                        "10M",
                        null,
                        null,
                        null,
                        null,
                        $this->translator->trans(
                            "The file size exceeds the allowed limit of {{ limit }} {{ suffix }}",
                            ['%limit%' => '{{ limit }}', '%suffix%' => '{{ suffix }}']
                        ),
                        "No file has been uploaded",
                        null,
                        null,

                    )]
                )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileData::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'upload',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
