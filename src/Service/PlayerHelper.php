<?php

namespace App\Service;

use App\Entity\Gender;
use App\Entity\Skill;
use App\Repository\GenderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlayerHelper
{
    private $entityManager;
    private $genderRepository;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        GenderRepository $genderRepository
    ) {
        $this->entityManager = $entityManager;
        $this->genderRepository = $genderRepository;
    }

    public function getGender(string $name): Gender
    {
        $gender = $this->genderRepository->findOneBy(['name' => $name]);
        if ($gender instanceof Gender)
            return $gender;

        $gender = new Gender();
        $gender->setName($name);

        $this->entityManager->persist($gender);
        $this->entityManager->flush();

        return $gender;
    }

    public function getSkill(string $name): Skill
    {
        $skill = $this->genderRepository->findOneBy(['name' => $name]);
        if ($skill instanceof Skill)
            return $skill;

        $skill = new Skill();
        $skill->setName($name);

        $this->entityManager->persist($skill);
        $this->entityManager->flush();

        return $skill;
    }
}
