<?php

namespace App\Form;

use App\Entity\DynamicForm;
use Exception;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dynamicForm = $options['dynamic_form'] ?? null;

        if (!$dynamicForm instanceof DynamicForm) {
            throw new InvalidArgumentException('You must use a DynamicForm object for dynamic_form option');
        }

        foreach ($dynamicForm->getInputs() as $input) {
            if ($input->getName() === null) {
                break;
            }
            $options = $input->getOptions() ?? [];
            $options['data'] = null;

            $builder->add($input->getName(), $input->getType()?->value, $options);

            if ($input->getDisplayRule()) {
                $builder->addEventListener(
                    FormEvents::PRE_SET_DATA,
                    function (FormEvent $event) use ($input) {
                        $form = $event->getForm();
                        $data = $event->getData();

                        if (!is_array($data)) {
                            $data = ['data' => $data];
                        }

                        try {
                            $expressionLanguage = new ExpressionLanguage();
                            if (!$expressionLanguage->evaluate($input->getDisplayRule(), $data)) {
                                $form->remove($input->getName());
                            }
                        } catch (Exception $exception) {
                            $form->remove($input->getName());
                        }
                    }
                );
            }
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'dynamic_form' => null,
        ]);

        $resolver->setRequired('dynamic_form');
        $resolver->setAllowedTypes('dynamic_form', DynamicForm::class);
    }
}
