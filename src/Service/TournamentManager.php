<?php

namespace App\Service;

use App\Entity\Encounter;
use App\Entity\Tournament;
use App\Repository\EncounterRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;

class TournamentManager
{
    private ?array $playersAvailable;
    private ?array $encounters;
    private ?Tournament $tournament;

    public function __construct(
        private TournamentRepository $tournamentRepository,
        private PlayerRepository $playerRepository,
        private EncounterRepository $encounterRepository
    ) {
        $this->playersAvailable = null;
        $this->encounters = null;
    }

    public function init(int $tournamentID): bool
    {
        $tournament = $this->tournamentRepository->findOneJoinedByID($tournamentID);

        if(is_null($tournament))
            return false;

        $this->tournament = $tournament;

        return true;
    }

    public function getRandomEncounter(): ?Encounter
    {
        return null;
    }

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function getPlayersAvailable(): array
    {
        if(is_null($this->playersAvailable))
            $this->playersAvailable = $this->playerRepository->findAllAvailable($this->tournament);

        return $this->playersAvailable;
    }

    public function getEncounters(): array
    {
        if(is_null($this->encounters))
            $this->encounters = $this->encounterRepository->findAllJoinedByTournament($this->tournament);

        return $this->encounters;
    }
}
