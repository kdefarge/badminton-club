<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['list_encounter'])]
    private ?int $number = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['list_encounter'])]
    private ?int $scoreTeam1 = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['list_encounter'])]
    private ?int $scoreTeam2 = null;

    #[ORM\ManyToOne(inversedBy: 'scores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Encounter $encounter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getScoreTeam1(): ?int
    {
        return $this->scoreTeam1;
    }

    public function setScoreTeam1(int $scoreTeam1): static
    {
        $this->scoreTeam1 = $scoreTeam1;

        return $this;
    }

    public function getScoreTeam2(): ?int
    {
        return $this->scoreTeam2;
    }

    public function setScoreTeam2(int $scoreTeam2): static
    {
        $this->scoreTeam2 = $scoreTeam2;

        return $this;
    }

    public function getEncounter(): ?Encounter
    {
        return $this->encounter;
    }

    public function setEncounter(?Encounter $encounter): static
    {
        $this->encounter = $encounter;

        return $this;
    }
}
