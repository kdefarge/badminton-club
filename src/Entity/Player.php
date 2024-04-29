<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    /**
     * @var Collection<int, Tournament>
     */
    #[ORM\ManyToMany(targetEntity: Tournament::class, mappedBy: 'playersAvailable')]
    private Collection $tournaments;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Gender $gender = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Skill $skill = null;

    /**
     * @var Collection<int, EncounterPlayer>
     */
    #[ORM\OneToMany(targetEntity: EncounterPlayer::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $encounterPlayers;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->encounterPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Tournament>
     */
    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    public function addTournament(Tournament $tournament): static
    {
        if (!$this->tournaments->contains($tournament)) {
            $this->tournaments->add($tournament);
            $tournament->addPlayersAvailable($this);
        }

        return $this;
    }

    public function removeTournament(Tournament $tournament): static
    {
        if ($this->tournaments->removeElement($tournament)) {
            $tournament->removePlayersAvailable($this);
        }

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * @return Collection<int, EncounterPlayer>
     */
    public function getEncounterPlayers(): Collection
    {
        return $this->encounterPlayers;
    }

    public function addEncounterPlayer(EncounterPlayer $encounterPlayer): static
    {
        if (!$this->encounterPlayers->contains($encounterPlayer)) {
            $this->encounterPlayers->add($encounterPlayer);
            $encounterPlayer->setPlayer($this);
        }

        return $this;
    }

    public function removeEncounterPlayer(EncounterPlayer $encounterPlayer): static
    {
        if ($this->encounterPlayers->removeElement($encounterPlayer)) {
            // set the owning side to null (unless already changed)
            if ($encounterPlayer->getPlayer() === $this) {
                $encounterPlayer->setPlayer(null);
            }
        }

        return $this;
    }
    
    public function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }
}
