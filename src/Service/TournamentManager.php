<?php

namespace App\Service;

use App\Entity\Encounter;
use App\Entity\EncounterPlayer;
use App\Entity\PlayerAffinity;
use App\Entity\Tournament;
use App\Repository\EncounterRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TournamentManager
{
    private ?array $playersAvailable;
    private ?array $encounters;
    private ?Tournament $tournament;

    public function __construct(
        private TournamentRepository $tournamentRepository,
        private PlayerRepository $playerRepository,
        private EncounterRepository $encounterRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
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
        return $affinity;
    }

    public function generationV2()
    {
        $affinity = [];
        foreach ($this->getEncounters() as $encounter) {

            $notAvailable = is_null($encounter->isTeam1Won());

            foreach ($encounter->getEncounterPlayers() as $encounterPlayer) {

                $player1Id = $encounterPlayer->getPlayer()->getId();
                if (!isset($affinity[$player1Id]))
                    $affinity[$player1Id] = new PlayerAffinity($encounterPlayer->getPlayer());

                /** @var PlayerAffinity $playerAffinity */
                $playerAffinity = $affinity[$player1Id];

                if ($notAvailable) {
                    $playerAffinity->setNotAvailable();
                    continue;
                }

                $player1IsTeam1 = $encounterPlayer->isTeam1();

                foreach ($encounter->getEncounterPlayers() as $encounterPlayer) {

                    $player2 = $encounterPlayer->getPlayer();

                    if ($player1Id == $player2->getId())
                        continue;

                    if ($encounterPlayer->isTeam1() == $player1IsTeam1) {
                        $playerAffinity->playTogether($player2);
                    } else {
                        $playerAffinity->playAgainst($player2);
                    }
                }

                foreach ($encounter->getScores() as $score) {
                    $scoreT1 = $score->getScoreTeam1();
                    $scoreT2 = $score->getScoreTeam2();
                    $playerAffinity->addDifference($player1IsTeam1 ? $scoreT1 - $scoreT2 : $scoreT2 - $scoreT1);
                    $playerAffinity->addPlayed($scoreT1 + $scoreT2);
                }
            }
        }

        $played = array_column($affinity, 'played');
        $difference = array_column($affinity, 'difference');
        array_multisort($played, SORT_ASC, $difference, SORT_ASC, $affinity);

        $playersAffinityAvailable = array_filter($affinity, function ($value, $key) {
            return $value->isAvailable() && $this->tournament->getEntrants()->contains($value->getPlayer());
        }, ARRAY_FILTER_USE_BOTH);

        dump($playersAffinityAvailable);

        if (is_null($this->playersAvailable))
            $this->playersAvailable = array_column($playersAffinityAvailable, 'player');
        //$playersAvailable = $this->getPlayersAvailable();


        $pairs = [];
        while (count($playersAffinityAvailable)>1) {
            /** @var PlayerAffinity $player1Affinity */
            $player1Affinity = array_shift($playersAffinityAvailable);
            $player1Id = $player1Affinity->getPlayer()->getId();
            /** @var PlayerAffinity $player2Affinity */
            foreach ($playersAffinityAvailable as $player2Affinity) {
                $player2Id = $player2Affinity->getPlayer()->getId();
                if ($player1Affinity->getLastPlayerId() == $player2Id)
                    continue;
                $pairs[$player1Id][$player2Id] = ($player1Affinity->getTogether($player2Id) * 100) + $player1Affinity->getAgainst($player2Id);
            }
            $this->logger->info(json_encode($pairs[$player1Id]));
            asort($pairs[$player1Id]);
            $this->logger->info(json_encode($pairs[$player1Id]));
        }
        
        $encounters = [];
        foreach ($pairs as $player1Id => $player1pairs) {
            unset($pairs[$player1Id]);
            foreach($player1pairs as $player2Id => $pairAffinity) {
                foreach ($pairs as $player3Id => $player2Affinity) {
                    
                }
            }
        }

        return $affinity;
    }
}
