<?php

namespace App\Form\Admin;

use App\Entity\DynamicFormInput;
use App\Form\Config\FormTypes;
use App\Form\Input\JsonTextAreaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DynamicFormInputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('label', TextType::class)
            ->add('type', EnumType::class, [
                'class' => FormTypes::class,
                'placeholder' => 'form.admin.select.type',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('step', IntegerType::class, [
                'attr' => ['min' => 1],
            ])
            ->add('sort', IntegerType::class, [
                'attr' => ['min' => 0],
            ])
            ->add('options', JsonTextAreaType::class, [
                'attr' => ['rows' => '8'],
            ])
            ->add('displayRule', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DynamicFormInput::class,
        ]);
    }
}
