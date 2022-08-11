<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class ManageUserSearchType extends AbstractType{

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
            ->add('category', ChoiceType::class, [
                'required'  => false,
                'choices' => array(
                    '' => null,
                    'Soccer' => 0,
                    'Basketball' => 1,
                )
            ])

            ->add('from',DateType::class, [
                'required' => false,
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day'
                )
            ])
            ->add('to', DateType::class, [
                'required'  => false,

                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day'
                )
            ])
            ->add('Search', SubmitType::class)
            ->add('Reset',ResetType::class);
    }
}

?>