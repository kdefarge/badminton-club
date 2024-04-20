<?php

namespace App\Service;

use App\Entity\Encounter;
use App\Entity\EncounterPlayer;
use App\Entity\Player;

class EncounterHelper
{
    public function getEncounterPlayer(Player $player, bool $isTeam1 = true): EncounterPlayer
    {
        $encounterPlayer = new EncounterPlayer();
        $encounterPlayer->setPlayer($player);
        $encounterPlayer->setTeam1($isTeam1);
        return $encounterPlayer;
    }
}
