<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    const GENDER_MAN = 'man';
    const GENDER_WOMAN = 'woman';

    const SKILL_BEGINNER = 'beginner';
    const SKILL_MIDDLE = 'middle';
    const SKILL_HIGH = 'high';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('man', 'woman')")]
    private ?string $gender = null;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('beginner', 'middle', 'high')")]
    private ?string $skill = null;

    /**
     * @var Collection<int, Tournament>
     */
    #[ORM\ManyToMany(targetEntity: Tournament::class, mappedBy: 'playersAvailable')]
    private Collection $tournaments;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        if (!in_array($gender, array(self::GENDER_MAN, self::GENDER_WOMAN))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->gender = $gender;

        return $this;
    }

    public function getSkill(): ?string
    {
        return $this->skill;
    }

    public function setSkill(string $skill): static
    {
        if (!in_array($skill, array(self::SKILL_BEGINNER, self::SKILL_MIDDLE, self::SKILL_HIGH))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->skill = $skill;

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
}
