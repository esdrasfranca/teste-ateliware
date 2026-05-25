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

    public function test_poke_api_client_fetch_all()
    {
        $limit = 100;
        $offset = 0;

        $pokeApiClient = new PokeApiClient();
        $result = $pokeApiClient->fetch($limit, $offset);

        // Verifica se o retorno é um array
        $this->assertIsArray($result);

        // Verifica se tem 100 registros
        $this->assertEquals($limit, count($result));
    }
}
