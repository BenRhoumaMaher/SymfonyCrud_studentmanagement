<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $student = new Student();
            $student->setName('Student '.$i);
            $student->setPlace('Place '.$i);
            $student->setPhone('Phone '.$i);
            $student->setDate(new \DateTimeImmutable());
            $manager->persist($student);
        }

        $manager->flush();
    }
}
