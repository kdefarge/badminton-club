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
            $encounterPlayer->setIsTeam1($key % 2 == 1);
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

    const COUNT_TOGETHER        = 0;
    const COUNT_AGAINST         = 1;
    const COUNT_POINTS_PLAYED   = 2;
    const COUNT_DIFFERENCE      = 3;

    public function generation()
    {
        $affinity = [];
        foreach ($this->getEncounters() as $encounter) {

            /** @var Encounter $encounter */
            foreach ($encounter->getEncounterPlayers() as $encounterPlayer) {

                $player1Id = $encounterPlayer->getPlayer()->getId();
                $player1IsTeam1 = $encounterPlayer->isTeam1();

                foreach ($encounter->getEncounterPlayers() as $encounterPlayer) {

                    $player2Id = $encounterPlayer->getPlayer()->getId();
                    $countType = ($encounterPlayer->isTeam1() == $player1IsTeam1) ? SELF::COUNT_TOGETHER : SELF::COUNT_AGAINST;

                    if ($player1Id == $player2Id)
                        continue;

                    $affinity[$player1Id][$countType][$player2Id] = ($affinity[$player1Id][$countType][$player2Id] ?? 0) + 1;
                }

                foreach ($encounter->getScores() as $score) {
                    $player1Diff = $player1IsTeam1 ? $score->getScoreTeam1() - $score->getScoreTeam2() : $score->getScoreTeam2() - $score->getScoreTeam1();
                    $pointsPlayed = $score->getScoreTeam1() + $score->getScoreTeam2();
                    $affinity[$player1Id][SELF::COUNT_DIFFERENCE] = ($affinity[$player1Id][SELF::COUNT_DIFFERENCE] ?? 0) + $player1Diff;
                    $affinity[$player1Id][SELF::COUNT_POINTS_PLAYED] = ($affinity[$player1Id][SELF::COUNT_POINTS_PLAYED] ?? 0) + $pointsPlayed;
                }
            }
        }
    }
}
