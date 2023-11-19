<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 20; $i++) {
            $equipement = new Equipment();
            $equipement->setName("Name " . $i)
            ->setCategory("Category" . $i)
            ->setNumber("Number " . $i)
            ->setDescription("Description" . $i);
            $manager->persist($equipement);
        }
        $manager->flush();
    }
}
