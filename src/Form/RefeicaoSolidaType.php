<?php

namespace App\Form;

use App\Entity\RefeicaoSolida;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefeicaoSolidaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('volume', IntegerType::class, [
                'label' => 'Quantidade (gramas)',
                'rounding_mode' => \NumberFormatter::ROUND_DOWN
            ])
            ->add('anotacao', TextareaType::class, [
                'label' => 'Descrição',
                'attr' => [
                    'rows' => 8
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RefeicaoSolida::class,
        ]);
    }
}
