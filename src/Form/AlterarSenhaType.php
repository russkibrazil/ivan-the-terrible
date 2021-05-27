<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlterarSenhaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('senha', PasswordType::class, [
                'label' => 'Senha atual'
            ])
            ->add('novaSenha', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'As senhas devem ser iguais',
                'first_options' => [
                    'label' => 'Nova senha'
                ],
                'second_options' => [
                    'label' => 'Repita a nova senha'
                ],
            ])
        ;
        // CHECK Validação possível?
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
