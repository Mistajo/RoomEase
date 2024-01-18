<?php

namespace App\DataFixtures;

use Faker;


use App\Entity\Equipment;

use App\Entity\MeetingRoom;

use Doctrine\Persistence\ObjectManager;;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MeetingRoomFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    private static $articleImages = [
        'room_1.jpg',
        'room_2.jpg',
        'room_4.jpg',
        'room_5.jpg',
        'room_3.jpg',
        'room_6.jpg',
        'room_7.jpg',
        'room_8.jpg',
        'room_10.jpg',
        'room_11.jpg',
        'room_12.jpg',
        'room_13.jpg',
        'room_14.jpg',
        'room_16.jpg',
        'room_17.jpg',
        'room_18.jpg',
        'room_19.jpg',
        'room_20.jpg',
        'room_21.jpg',
        'room_22.jpg',
        'salle-de-reunion (1).jpg',
        'TableXP18pers-immersif2.jpg',
    ];

    private static $articleequipment = [
        'Paperboard',
        'Vidéoprojecteur',
        'Wifi',
        'Equipement son',
        'Ecran LCD',
        'Micro',
        'Blocs-notes & stylo',
        'Climatisation',
    ];




    public function __construct(UserPasswordHasherInterface $hasher, ParameterBagInterface $params)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void

    {

        $equipmentObjects = [];
        foreach (self::$articleequipment as $name) {
            $equipment = new Equipment();
            $equipment->setName($name);
            $equipmentObjects[] = $equipment;
        }

        // initialisation de l'objet Faker
        $faker = Faker\Factory::create('fr_FR');
        $meetingrooms = array();
        for (
            $i = 1;
            $i < 100;
            $i++
        ) {
            $meetingrooms[$i] = new MeetingRoom();
            $meetingrooms[$i]->setName($faker->unique()->word);
            $meetingrooms[$i]->setCapacity($faker->numberBetween(5, 500));
            $meetingrooms[$i]->setDescription($faker->realText(500));
            $meetingrooms[$i]->setIsAvailable(true);

            $meetingrooms[$i]->setImage($faker->randomElement(self::$articleImages));
            // $meetingrooms[$i]->addEquipment($faker->randomElement(self::$articleequipment));

            foreach ($faker->randomElements($equipmentObjects, $count = 3) as $equipment) {
                $meetingrooms[$i]->addEquipment($equipment);
            }



            $manager->persist($meetingrooms[$i]);
        }


        $manager->flush();
    }
}