<?php

namespace AppBundle\Form\Type;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('FirstName',TextType::class
            )
            ->add('LastName',TextType::class
            )
            ->add('userEmail',TextType::class
            )
            ->add('plainedPassword',RepeatedType::class,[
                'type'=>PasswordType::class,

                ]
            )
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class'=>User::class
        ]);
    }
}


?>