<?php

namespace AppBundle\Form\Type;

use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class userType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('userName', TextType::class, [
                'required'  => false
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array(
                    ''=> null,
                    'Active' => 1,
                    'Blocked' => 0,
                )
            ])
            ->add('registration',DateType::class)
            ->add('Search', SubmitType::class)
            ->add('Reset',ResetType::class)
        ;
    }
}

