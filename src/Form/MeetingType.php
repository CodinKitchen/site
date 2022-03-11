<?php

namespace App\Form;

use App\Entity\Meeting;
use App\Repository\ScheduleRuleRepository;
use DateTime;
use DateTimeInterface;
use Recurr\Recurrence;
use Recurr\Transformer\ArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingType extends AbstractType
{
    public function __construct(private ScheduleRuleRepository $scheduleRuleRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $scheduleRules = $this->scheduleRuleRepository->findAll();

        $dates = [];
        foreach ($scheduleRules as $scheduleRule) {
            if (($rule = $scheduleRule->getRule()) !== null) {
                $rule->setUntil(new DateTime('now + 14 days'));
                $rule->setStartDate((new DateTime())->setTime(0, 0));
                $transformer = new ArrayTransformer();
                /** @var Recurrence[] $recurrences */
                $recurrences = $transformer->transform($rule)->toArray();
                $dates += $recurrences;
            }
        }

        $dates = array_map(function (Recurrence $recurrence): DateTimeInterface {
            return $recurrence->getStart();
        }, $dates);

        dump($dates);

        $builder
            ->add('timeSlot', ChoiceType::class, [
                'choices' => $dates,
            ])
            ->add('duration', ChoiceType::class, [
                'choices' => ["1h" => 1, "2h" => 2],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meeting::class,
        ]);
    }
}
