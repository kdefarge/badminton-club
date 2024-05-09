<?php

namespace App\Entity;

use App\Repository\EncounterPlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EncounterPlayerRepository::class)]
class EncounterPlayer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['list_encounter'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['list_encounter'])]
    private ?bool $isTeam1 = null;

    #[ORM\ManyToOne(inversedBy: 'encounterPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Encounter $encounter = null;

    #[ORM\ManyToOne(inversedBy: 'encounterPlayers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['list_encounter'])]
    private ?Player $player = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isTeam1(): ?bool
    {
        return $this->isTeam1;
    }

    public function setIsTeam1(bool $isTeam1): static
    {
        $this->isTeam1 = $isTeam1;

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

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }
}
