<?php

namespace Tests\Feature;

use App\DTOs\PokemonDto;
use App\Services\PokeApiClient;
use App\Exceptions\HttpNotFoundException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PokeApiClientTest extends TestCase
{
    private string $baseUrl = 'https://pokeapi.co/api/v2/pokemon';

    /**
     * Testa a busca com sucesso de um Pokémon individual
     */
    public function test_poke_api_client_fetch_pokemon_successfully(): void
    {
        Http::fake([
            "{$this->baseUrl}/pikachu" => Http::response([
                'name' => 'pikachu',
                'height' => 4,
                'weight' => 60,
                'sprites' => ['front_default' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png'],
                'types' => [
                    ['type' => ['name' => 'electric']]
                ],
                'stats' => [
                    ['base_stat' => 35, 'stat' => ['name' => 'hp']],
                    ['base_stat' => 55, 'stat' => ['name' => 'attack']]
                ]
            ], 200)
        ]);

        $pokeApiClient = new PokeApiClient();
        $pokemonDto = $pokeApiClient->fetchPokemon('pikachu');

        $this->assertInstanceOf(PokemonDto::class, $pokemonDto);
        $this->assertEquals('pikachu', $pokemonDto->getName());
        $this->assertEquals(35, $pokemonDto->getHp());
    }

    /**
     * Testa a busca concorrente em paralelo
     */
    public function test_poke_api_client_fetch_concurrent_successfully(): void
    {
        Http::fake([
            "{$this->baseUrl}/pikachu" => Http::response([
                'name' => 'pikachu',
                'height' => 4,
                'weight' => 60,
                'sprites' => ['front_default' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png'],
                'stats' => [['base_stat' => 35, 'stat' => ['name' => 'hp']]]
            ], 200),
            "{$this->baseUrl}/charizard" => Http::response([
                'name' => 'charizard',
                'height' => 17,
                'weight' => 905,
                'sprites' => ['front_default' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png'],
                'stats' => [['base_stat' => 78, 'stat' => ['name' => 'hp']]]
            ], 200)
        ]);

        $pokeApiClient = new PokeApiClient();
        $result = $pokeApiClient->fetchConcurrent('pikachu', 'charizard');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(PokemonDto::class, $result[0]);
        $this->assertInstanceOf(PokemonDto::class, $result[1]);

        $this->assertEquals('pikachu', $result[0]->getName());
        $this->assertEquals('charizard', $result[1]->getName());
    }

    /**
     * Testa quando o Pokémon não existe
     */
    public function test_poke_api_client_fetch_pokemon_throws_not_found_exception(): void
    {
        Http::fake([
            "{$this->baseUrl}/invalidpokemon" => Http::response([], 404)
        ]);

        $pokeApiClient = new PokeApiClient();

        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage("O Pokémon 'invalidpokemon' não existe.");

        $pokeApiClient->fetchPokemon('invalidpokemon');
    }

    /**
     * Testa a listagem
     */
    public function test_poke_api_client_fetch_all_paginated(): void
    {
        $limit = 100;
        $offset = 0;

        Http::fake([
            "{$this->baseUrl}*" => Http::response([
                'results' => array_fill(0, 100, ['name' => 'bulbasaur', 'url' => '...'])
            ], 200)
        ]);

        $pokeApiClient = new PokeApiClient();
        $result = $pokeApiClient->fetch($limit, $offset);

        $this->assertIsArray($result);
        $this->assertCount($limit, $result);
    }
}
