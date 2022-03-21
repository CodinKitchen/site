<?php

namespace App\Form;

use App\Dto\MeetingRequestDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'input'  => 'datetime_immutable',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
            ])
            ->add('time', TimeType::class, [
                'input'  => 'datetime_immutable',
                'widget' => 'single_text',
                'input_format' => 'H:i'
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'form.meeting.duration.label',
                'choices' => ["1h" => 1, "2h" => 2],
                ])
            ->add('note', TextareaType::class, [
                    'label' => 'form.meeting.note.label',
                    'required' => false,
            ])
            ->add('paymentMethod', HiddenType::class, [
                'attr' => ['data-payment-target' => 'paymentMethod'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MeetingRequestDto::class,
        ]);
    }
}
