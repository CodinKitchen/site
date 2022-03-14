<?php

namespace App\Form;

use App\Entity\Meeting;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'mapped' => false,
                'input'  => 'string',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
            ])
            ->add('time', TimeType::class, [
                'mapped' => false,
                'input'  => 'string',
                'widget' => 'single_text',
                'input_format' => 'H:i'
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'form.meeting.duration.label',
                'choices' => ["1h" => 1, "2h" => 2],
            ])
            ->addEventListener(FormEvents::SUBMIT, [$this, 'mapDateAndTimeToTimeSlot'])
        ;
    }

    public function mapDateAndTimeToTimeSlot(FormEvent $formEvent): void
    {
        $form = $formEvent->getForm();

        $date = $form->get('date')->getData();
        $time = $form->get('time')->getData();

        if (empty($date) || empty($time)) {
            return;
        }

        $timeSlot = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

        if (!$timeSlot) {
            return;
        }

        /** @var Meeting $meeting */
        $meeting = $form->getData();
        $meeting->setTimeSlot($timeSlot);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meeting::class,
        ]);
    }
}
