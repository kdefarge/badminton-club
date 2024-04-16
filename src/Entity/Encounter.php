<?php

namespace App\Entity;

use App\Repository\EncounterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EncounterRepository::class)]
class Encounter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isFinished = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isTeam1Won = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, EncounterSetResult>
     */
    #[ORM\OneToMany(targetEntity: EncounterSetResult::class, mappedBy: 'encounter', orphanRemoval: true)]
    private Collection $setResults;

    /**
     * @var Collection<int, EncounterPlayer>
     */
    #[ORM\OneToMany(targetEntity: EncounterPlayer::class, mappedBy: 'encounter', orphanRemoval: true)]
    private Collection $encounterPlayers;

    #[ORM\ManyToOne(inversedBy: 'encounters')]
    private ?Tournament $tournament = null;

    public function __construct()
    {
        $this->setResults = new ArrayCollection();
        $this->encounterPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    public function isTeam1Won(): ?bool
    {
        return $this->isTeam1Won;
    }

    public function setTeam1Won(?bool $isTeam1Won): static
    {
        $this->isTeam1Won = $isTeam1Won;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, EncounterSetResult>
     */
    public function getSetResults(): Collection
    {
        return $this->setResults;
    }

    public function addSetResult(EncounterSetResult $setResult): static
    {
        if (!$this->setResults->contains($setResult)) {
            $this->setResults->add($setResult);
            $setResult->setEncounter($this);
        }

        return $this;
    }

    public function removeSetResult(EncounterSetResult $setResult): static
    {
        if ($this->setResults->removeElement($setResult)) {
            // set the owning side to null (unless already changed)
            if ($setResult->getEncounter() === $this) {
                $setResult->setEncounter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EncounterPlayer>
     */
    public function getPlayers(): Collection
    {
        return $this->encounterPlayers;
    }

    public function addPlayer(EncounterPlayer $encounterPlayer): static
    {
        if (!$this->encounterPlayers->contains($encounterPlayer)) {
            $this->encounterPlayers->add($encounterPlayer);
            $encounterPlayer->setEncounter($this);
        }

        return $this;
    }

    public function removePlayer(EncounterPlayer $encounterPlayer): static
    {
        if ($this->encounterPlayers->removeElement($encounterPlayer)) {
            // set the owning side to null (unless already changed)
            if ($encounterPlayer->getEncounter() === $this) {
                $encounterPlayer->setEncounter(null);
            }
        }

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): static
    {
        $this->tournament = $tournament;

        return $this;
    }
}
