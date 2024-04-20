<?php

namespace App\Service;

use App\Entity\Encounter;
use App\Entity\Player;
use Exception;

class EncounterGenerator
{
    

    public function getRandomEncounter(array $players): Encounter
    {
        $nb_player = count($players);

        if($nb_player < 2 || $nb_player > 4) 
            throw new Exception('Invalid players number '.$nb_player.'. There must be between 2 and 4 players');

        $encounter = new Encounter();

        return $encounter;
    }

}
