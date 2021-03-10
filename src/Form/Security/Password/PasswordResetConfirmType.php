<?php
namespace App\Form\Security\Password;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Auth\Password\Data\PasswordResetConfirmData;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordResetConfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class,[])
            ->add('confirmPassword', PasswordType::class,[])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordResetConfirmData::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
