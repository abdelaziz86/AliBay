<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class,[
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('email', TextType::class,[
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('password', PasswordType::class,[
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('confirm_password', PasswordType::class,[
                'attr' => [
                    'class'=>'form-control'
                ]
            ])
            ->add('refered', TextType::class,[
                'attr' => [
                    'class'=>'form-control' 
                    
                ],
                'label' =>'Code Promo (Optionnel)',
                'required' => false 
            ]) ; 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
