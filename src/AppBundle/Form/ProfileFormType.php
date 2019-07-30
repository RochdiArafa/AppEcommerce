<?php
/**
 * Created by PhpStorm.
 * User: ahmed
 * Date: 09/02/2019
 * Time: 16:27
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('avatar',FileType::class, array('data_class' => null))
                ->add('nom')
                ->add('prenom')
                ->add('tel',TextType::class)
                ->add('adresse')
                ->add('username');
    }

    public function getParent(){
 return BaseProfileFormType::class;}
}