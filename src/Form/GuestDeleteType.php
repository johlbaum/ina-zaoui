<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GuestDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Supprimer le compte',
                'attr' => ['class' => 'btn btn-danger']
            ]);
    }
}
