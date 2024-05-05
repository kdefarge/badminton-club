<?php

namespace App\Service;

use App\Entity\Encounter;
use App\Entity\EncounterPlayer;
use App\Entity\Tournament;
use App\Repository\EncounterRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;

class TournamentManager
{
    private ?array $playersAvailable;
    private ?array $encounters;
    private ?Tournament $tournament;

    public function __construct(
        private TournamentRepository $tournamentRepository,
        private PlayerRepository $playerRepository,
        private EncounterRepository $encounterRepository,
        private EntityManagerInterface $entityManager
    ) {
        $this->playersAvailable = null;
        $this->encounters = null;
    }

    public function init(int $tournamentID): bool
    {
        $tournament = $this->tournamentRepository->findOneJoinedByID($tournamentID);

        if (is_null($tournament))
            return false;

        $this->tournament = $tournament;

        return true;
    }

    public function getRandomEncounter(): ?Encounter
    {
        $playersAvailable = $this->getPlayersAvailable();
        if (count($playersAvailable) < 4)
            return null;

        $encounter = new Encounter();
        $encounter->setCreatedAt(date_create_immutable());
        $encounter->setTournament($this->tournament);

        $this->entityManager->persist($encounter);

        $randKeys = array_rand($playersAvailable, 4);

        foreach ($randKeys as $key => $randKey) {
            $encounterPlayer = new EncounterPlayer();
            $encounterPlayer->setPlayer($playersAvailable[$randKey]);
            $encounterPlayer->setIsTeam1(($key + 1) % 1 ? true : false);
            $encounterPlayer->setEncounter($encounter);
            $this->entityManager->persist($encounterPlayer);
        }

        $this->entityManager->flush();

        return $encounter;
    }

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function getPlayersAvailable(): array
    {
        if (is_null($this->playersAvailable))
            $this->playersAvailable = $this->playerRepository->findAllAvailable($this->tournament);

        return $this->playersAvailable;
    }

    public function getEncounters(): array
    {
        if (is_null($this->encounters))
            $this->encounters = $this->encounterRepository->findAllJoinedByTournament($this->tournament);

        return $this->encounters;
    }
}
