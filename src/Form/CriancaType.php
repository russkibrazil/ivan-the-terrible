<?php

namespace App\Form;

use App\Entity\Crianca;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriancaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome'
            ])
            ->add('dn', DateType::class, [
                'label' => 'Nascimento'
            ])
            ->add('foto', null, [])
            ->add('parentesco', TextType::class, [
                'label' => 'Parentesco',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Crianca::class,
        ]);
    }
}
