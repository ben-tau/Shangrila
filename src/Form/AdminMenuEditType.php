<?php

namespace App\Form;

use App\Entity\Menu;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminMenuEditType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $constraints = [new File([
        //     'maxSize' => '10000k',
        //     'mimeTypes' => [
        //         'image/jpeg',
        //         'image/png'
        //     ],
        //     'mimeTypesMessage' => 'Veuillez télécharger un document avec un format valide (.jpeg, .jpg, .png)'
        // ])];

        $builder
            ->add('title',TextType::class,$this->getConfiguration("Titre du menu","Donnez un titre à votre menu"))
            ->add('text',TextareaType::class,$this->getConfiguration("Descriptif du menu","Renseignez ici votre menu"))
            ->add('price',MoneyType::class,$this->getConfiguration("Prix du menu","Prix"))
            ->add('availability',ChoiceType::class,$this->getConfigurationChoiceTypeInput("Votre menu est-il disponible ?",['Oui'=>true,'Non'=>false]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
