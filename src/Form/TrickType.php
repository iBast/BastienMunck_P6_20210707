<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Trick\'s name :',
                'attr' => [
                    'placeholder' => 'Enter the trick name here'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description :',
                'attr' => [
                    'placeholder' => 'You can write here about the trick',
                    'rows' => 15
                ]
            ])
            ->add('mainPic', TextType::class, [
                'label' => 'Cover picture url :',
                'attr' => [
                    'placeholder' => 'Enter here the picture url'
                ]
            ])
            ->add('type', EntityType::class, [
                'label' => 'Trick\'s group :',
                'placeholder' => '-- Chose the trick group --',
                'class' => Group::class,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
