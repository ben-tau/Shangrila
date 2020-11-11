<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {   
        $user = new User();
     
        $user->setFirstname("Benjamin")
             ->setLastname("Taupiac")
             ->setEmail("bn82@hotmail.fr")
             ->setHash("password")
             ->setSlug("benjamin-taupiac")
        ;

        // $product = new Product();
        // $manager->persist($product);

        $manager->persist($user);
    
        $manager->flush();

        

    }
}
