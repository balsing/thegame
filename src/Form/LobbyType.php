<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LobbyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_game', SubmitType::class, [
                'label' => 'Создать новую игру',
                'attr' => [
                    'class'=> 'btn waves-effect waves-light'
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Введите код комнаты',
                'required' => false,
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Вход',
                'attr' => [
                    'class'=> 'btn waves-effect waves-light'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
