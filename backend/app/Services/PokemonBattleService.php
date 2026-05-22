<?php

namespace App\Services;

use App\DTOs\BattleResultDto;

class PokemonBattleService
{
    private string $pokemon1;
    private string $pokemon2;


    public function __construct(string $pokemon1, string $pokemon2)
    {
        $this->pokemon1 = $pokemon1;
        $this->pokemon2 = $pokemon2;
    }

    public function runBattle(): BattleResultDto
    {
        $pokemons = $this->getPokemons($this->pokemon1, $this->pokemon2);
        $pokemon1 = $pokemons[0];
        $pokemon2 = $pokemons[1];

        $winner = null;
        $message = "Empate";

        if ($pokemon1->getHp() > $pokemon2->getHp()) {
            $winner = $pokemon1;
            $message = $pokemon1->getName() . " venceu!";
        } else if ($pokemon1->getHp() < $pokemon2->getHp()) {
            $winner = $pokemon2;
            $message = ucwords($pokemon2->getName()) . " venceu!";
        }

        $winnerName = $winner ? $winner->getName() : '';
        return new BattleResultDto($pokemon1, $pokemon2, $winnerName, $message);
    }

    /**
     * Busca os pokemons na api externa e retorna um array de objetos PokemonDto
     */
    private function getPokemons(string $pokemonName1, string $pokemonName2): array
    {
        $pokeApiClient = new PokeApiClient();
        $pokemon1 = $pokeApiClient->fetchPokemon($pokemonName1);
        $pokemon2 = $pokeApiClient->fetchPokemon($pokemonName2);
        return array($pokemon1, $pokemon2);
    }
}
