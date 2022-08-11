<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class AddCategoryType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('CategoryName',TextType::class
            )
            ->add('ParentCategory', ChoiceType::class, [
                'choices' => [
                    'Sports' => 1,
                    'Acting'=> 2,
                    'Dancing'=>3,
                ]
            ])

            ->add('Submit',SubmitType::class);
    }}

?>
