<?php
/**
 * Created by PhpStorm.
 * User: ahmed
 * Date: 08/02/2019
 * Time: 22:10
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('avatar',FileType::class , array('data_class' => null), ['label' => ' '])->add('nom' , null,['label' => ' '])->
        add('prenom' , null,['label' => ' '])->add('tel',TextType::class , ['label' => ' '])->add('adresse' , null,['label' => ' '])->add('email' , null,['label' => ' '])->add('password' ,PasswordType::class,['label' => ' '])->add('plainPassword' , PasswordType::class,['label' => ' '])->remove('username');
    }

    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }

}