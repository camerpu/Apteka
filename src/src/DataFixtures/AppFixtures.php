<?php

namespace App\DataFixtures;

use App\Entity\Pharmacy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $pharmacy1 = new Pharmacy();
        $pharmacy1->setName('Apteka 1');
        $pharmacy1->setCity('Olsztyn');
        $pharmacy1->setPostalCode('10-699');
        $pharmacy1->setLongitude(0.0);
        $pharmacy1->setLatitude(0.0);
        $pharmacy1->setStreet('Urocza 1');
        $manager->persist($pharmacy1);

        $pharmacy2 = new Pharmacy();
        $pharmacy2->setName('Apteka 2');
        $pharmacy2->setCity('Olsztyn');
        $pharmacy2->setPostalCode('10-700');
        $pharmacy2->setLongitude(0.0);
        $pharmacy2->setLatitude(0.0);
        $pharmacy2->setStreet('Urocza 2');
        $manager->persist($pharmacy2);

        $pharmacy3 = new Pharmacy();
        $pharmacy3->setName('Apteka 3');
        $pharmacy3->setCity('Olszewo');
        $pharmacy3->setPostalCode('10-701');
        $pharmacy3->setLongitude(0.0);
        $pharmacy3->setLatitude(0.0);
        $pharmacy3->setStreet('Urocza 3');
        $manager->persist($pharmacy3);

        $manager->flush();
    }
}
