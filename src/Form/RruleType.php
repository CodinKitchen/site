<?php

namespace App\Form;

use Recurr\Exception\InvalidRRule;
use Recurr\Rule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RruleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                function (?Rule $rrule) {
                    return $rrule?->getString();
                },
                function (string $rruleString) {
                    try {
                        return new Rule($rruleString);
                    } catch (InvalidRRule $exception) {
                        throw new TransformationFailedException(sprintf('The value "%s" is not a valid RRULE.', $rruleString));
                    }
                }
            ))
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
        ]);
    }
}
