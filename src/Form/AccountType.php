<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class,$this->getConfiguration("Prénom","Votre prénom"))
            ->add('lastname',TextType::class,$this->getConfiguration("Nom","Votre nom"))
            ->add('email',EmailType::class,$this->getConfiguration("Email","Un email valide"))
            ->add('phone',TelType::class,$this->getConfiguration("N° de téléphone","Votre téléphone"))
            ->add('address',TextType::class,$this->getConfiguration("Adresse","Votre adresse"))
            ->add('postalCode',NumberType::class,$this->getConfiguration("Code Postal","Code Postal"))
            ->add('city',TextType::class,$this->getConfiguration("Ville","Ville"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
