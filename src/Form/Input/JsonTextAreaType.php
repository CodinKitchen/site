<?php

namespace App\Form\Input;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class JsonTextAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function (?array $data) {
                return $data !== null ? json_encode($data) : '';
            },
            function (?string $json) {
                return json_decode($json ?? '', true);
            }
        ));
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
