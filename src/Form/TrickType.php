<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    protected $pictureRepository;

    public function __construct(PictureRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }
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
            ->add('type', EntityType::class, [
                'label' => 'Trick\'s group :',
                'placeholder' => '-- Chose the trick group --',
                'class' => Group::class,
                'choice_label' => 'name'
            ])
            ->add('mainPicture', EntityType::class, [
                'class' => Picture::class,
                'query_builder' => function (PictureRepository $pr) use ($builder) {
                    if ($builder->getData()->getId()) { // only for edit form of an existing trick
                        return $pr->createQueryBuilder('p')
                            ->where('p.trick = ' . $builder->getData()->getId())
                            ->orderBy('p.id', 'ASC');
                    } else { // for new trick form
                        return null;
                    }
                },
                'choice_label' => function (Picture $picture) {
                    return '<img src="' . $picture->getpath() . '" alt="trick picture" class="img-fluid" type="button">';
                },
                'label_html' => true,
                'multiple' => false,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
