<?php

namespace App\Form;

use App\Entity\Style;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ColorType ;
use Symfony\Component\Form\Extension\Core\Type\SubmitType ; 



class StyleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('nameColor', ColorType::class,[
                'attr' => [
                    'class'=>'form-control',
                    'required' => false
                ]

            ])
            ->add('nameGlow', ColorType::class,[
                'attr' => [
                    'class'=>'form-control',
                    'required' => false 
                ]
            ]) 
            ->add('Enregistrer', SubmitType::class,[
                'attr' => [
                    'class'=>'btn btn-success waves-effect waves-light'
                ]
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Style::class,
        ]);
    }
}
