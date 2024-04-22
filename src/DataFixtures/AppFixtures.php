<?php

namespace App\DataFixtures;

use App\Config\PlayerGender;
use App\Config\PlayerSkill;
use App\Entity\Encounter;
use App\Entity\Gender;
use App\Entity\Player;
use App\Entity\Skill;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

        // Create genders based on PlayerGender
        $genders = [];

        foreach (PlayerGender::cases() as $playerGender) {
            $gender = new Gender();
            $gender->setName($playerGender->value);
            $manager->persist($gender);
            $genders[$playerGender->value] = $gender;
        }

        // Create genders based on Playerskill
        $skills = [];

        foreach (PlayerSkill::cases() as $playerSkill) {
            $skill = new Skill();
            $skill->setName($playerSkill->value);
            $manager->persist($skill);
            $skills[$playerSkill->value] = $skill;
        }

        // Create of 20 Players
        $players = [];

        for ($i = 0; $i < 20; $i++) {

            /** @var Gender $gender */
            $gender = $faker->randomElement($genders);

            /** @var Skill $skill */
            $skill = $faker->randomElement($skills);

            $player = new Player();
            $player->setFirstname($faker->firstName($gender->getName()));
            $player->setLastname($faker->lastName($gender->getName()));
            $player->setGender($gender);
            $player->setSkill($skill);
            $manager->persist($player);
            $players[] = $player;
        }

        // Create of 60 encounters

        for ($i = 0; $i < 60; $i++) {

            $encounter = new Encounter();
            $encounter->setFinished($faker->boolean(80));
            $createAt = $faker->dateTimeInInterval("-200 days", "-50 days", "Europe/Paris");
            $encounter->setCreatedAt(DateTimeImmutable::createFromMutable($createAt));
            if ($encounter->isFinished()) {
                $encounter->setTeam1Won($faker->boolean());
                $encounter->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeInInterval($createAt, "-1 days", "Europe/Paris")));
            }
            $manager->persist($encounter);

            /** @var Player $gender */
            $encounterPlayers = $faker->randomElements($players, 4);
        }

        $manager->flush();
    }
}
