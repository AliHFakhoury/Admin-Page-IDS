<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class AdminType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('Email',TextType::class)
            ->add('AdminType',ChoiceType::class,
                array('choices' => array(
                    'admin' => 1,
                    'Moderator' =>0,


                ),
                    'choices_as_values' => true,'multiple'=>false))

            ->add('Category', ChoiceType::class, [
                'choices' => [
                     'Football' => 1,
                     'basketBall'=> 2,
                     'Swimming'=>3,
                ]
            ])

            ->add('Submit',SubmitType::class);
}}

?>