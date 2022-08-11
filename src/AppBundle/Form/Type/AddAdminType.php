<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class AddAdminType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('username',HiddenType::class
            )

            ->add('firstName',TextType::class
            )
            ->add('lastName',TextType::class
            )
            ->add('email',EmailType::class
            )
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password'
                ]
            ])
            ->add('roleID',ChoiceType::class,
                array('choices' => array(
                    'admin' => '1',
                    'Moderator' => '0',

                ),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))


            ->add('category_ID', ChoiceType::class, [
                'choices' => [
                    'Football' => 1,
                    'basketBall'=> 2,
                    'Swimming'=>3,
                ]
            ])
            ->add('Submit',SubmitType::class)
            ->add('Clear', ResetType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>admin::class
        ]);
    }
}

?>