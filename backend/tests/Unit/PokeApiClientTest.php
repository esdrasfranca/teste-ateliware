<?php

namespace App\Services;

use App\DTOs\PokemonDto;
use PHPUnit\Framework\TestCase;

class PokeApiClientTest extends TestCase
{

    public function test_poke_api_client_fetch_pokemon()
    {
        $pokeApiClient = new PokeApiClient();
        $pokemonDto = $pokeApiClient->fetchPokemon('pikachu');

        // Verifica se o retorno é uma instância de PokemonDto
        $this->assertInstanceOf(PokemonDto::class, $pokemonDto);

        // Verifica se o nome do Pokémon é 'pikachu'
        $this->assertEquals('pikachu', $pokemonDto->getName());
    }
}
