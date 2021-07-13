<?php

namespace App\Form;

use App\Entity\Mamadeira;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MamadeiraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alimento', ChoiceType::class, [
                'choices' => [
                    'Leite Materno' => 'LEITEM',
                    'Leite/Fórmula' => 'LEITEF',
                    'Outros leites' => 'LEITED',
                    'Água' => 'AGUA',
                    'Chá' => 'CHA',
                    'Suco' => 'SUCO',
                ]

            ])
            ->add('volume', IntegerType::class, [
                'label' => 'Quantidade (ml)',
                'rounding_mode' => \NumberFormatter::ROUND_DOWN
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mamadeira::class,
        ]);
    }
}
