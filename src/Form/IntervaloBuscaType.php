<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class IntervaloBuscaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dInicio', DateType::class, [
                'label' => 'Início',
                'mapped' => false,
                'required' => true,
                'widget' => 'single_text',
                'input' => 'string',
                'constraints' => [
                    new Date(['message' => 'Data {{ value }} inválida para {{ label }}'])
                ]
            ])
            ->add('dFim', DateType::class, [
                'label' => 'Fim',
                'mapped' => false,
                'required' => false,
                'widget' => 'single_text',
                'input' => 'string',
                'constraints' => [
                    new Date(['message' => 'Data {{ value }} inválida para {{ label }}'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
