<?php

namespace App\Services;

use App\DTOs\BattleResultDto;

class PokemonBattleService
{
    public function __construct(
        private readonly PokeApiClient $pokeApiClient
    ) {}

    public function runBattle(string $pokemon1Name, string $pokemon2Name): BattleResultDto
    {
        $pokemons = $this->pokeApiClient->fetchConcurrent($pokemon1Name, $pokemon2Name);
        $pokemon1 = $pokemons[0];
        $pokemon2 = $pokemons[1];

        $winner = null;
        $message = "Empate";

        if ($pokemon1->getHp() > $pokemon2->getHp()) {
            $winner = $pokemon1;
            $message = ucwords($pokemon1->getName()) . " venceu!";
        } else if ($pokemon1->getHp() < $pokemon2->getHp()) {
            $winner = $pokemon2;
            $message = ucwords($pokemon2->getName()) . " venceu!";
        }

        return new BattleResultDto($pokemon1, $pokemon2, $winner, $message);
    }
}
