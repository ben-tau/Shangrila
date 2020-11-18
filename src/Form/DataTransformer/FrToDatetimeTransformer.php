<?php

use Symfony\Component\Validator\Constraints\DateTime;

    namespace App\Form\DataTransformer;
    
    use Symfony\Component\Form\DataTransformerInterface;

    class FrToDatetimeTransformer implements DataTransformerInterface
    {
        // Transforme les données originelles pour que l'on puisse avoir la bonne donnée à afficher
        public function transform($date)
        {
            if($date === null)
            {
                return '';
            }
            
            // retourne une date en fr
            return $date->format('d/m/Y H:i');
        }

        // Fait l'inverse de transform(), elle prend la donnée qui arrive du form et la remet ds le format que l'on attend
        public function reverseTransform($formDate)
        {
            // date en fr 02/11/2020

            if($formDate === null)
            {
                // exception

                throw new TransformationFailedException("Veuillez renseigner une date");
            }

            $date = \DateTime::createFromFormat('Y-m-d H:i:s',$formDate);

            if($date === false)
            {
                // exception

                throw new TransformationFailedException("Le format de la date n'est pas correct");
            }

            return $date;
        }
    }