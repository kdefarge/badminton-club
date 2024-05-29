<?php

namespace App\Entity;

class PlayerAffinity
{
    private array $together;
    private array $against;
    private int $played = 0;
    private int $difference = 0;
    private int $lastPlayerId = 0;
    private bool $available = true;

    public function __construct(private Player $player)
    {
        $this->together = [];
        $this->against = [];
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function playTogether(Player $player): static
    {
        $id = $player->getId();
        $this->together[$id] = ($this->together[$id] ?? 0) + 1;
        $this->setLastPlayerId($player->getId());

        return $this;
    }

    public function getTogether(int $playerId): int
    {
        return $this->together[$playerId] ?? 0;
    }

    public function playAgainst(Player $player): static
    {
        $id = $player->getId();
        $this->against[$id] = ($this->against[$id] ?? 0) + 1;

        return $this;
    }

    public function getAgainst(int $playerId): int
    {
        return $this->against[$playerId] ?? 0;
    }

    public function addPlayed(int $points): static
    {
        $this->played += $points;

        return $this;
    }

    public function getPlayed(): int
    {
        return $this->played;
    }

    public function addDifference(int $points): static
    {
        $this->difference += $points;

        return $this;
    }

    public function getDifference(): int
    {
        return $this->difference;
    }

    public function setLastPlayerId(int $playerId): static
    {
        if ($this->lastPlayerId == 0)
            $this->lastPlayerId = $playerId;

        return $this;
    }

    public function getLastPlayerId(): int
    {
        return $this->lastPlayerId;
    }

    public function setNotAvailable(): static
    {
        $this->available = false;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return isset($this->$prop);
    }
}
