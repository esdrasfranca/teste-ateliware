<?php

namespace Tests\Feature; // Alterado para Feature, pois testa a integração com o componente HTTP

use App\DTOs\BattleResultDto;
use App\Services\PokeApiClient;
use App\Services\PokemonBattleService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase; // IMPORTANTE: Estender o TestCase do Laravel para carregar as fachadas

class PokemonBattleTest extends TestCase
{
    private string $baseUrl = 'https://pokeapi.co/api/v2/pokemon';

    /**
     * Testar o cenário de vitória
     */
    public function test_pokemon_battle_victory(): void
    {
        Http::fake([
            "{$this->baseUrl}/pikachu" => Http::response([
                'name' => 'pikachu',
                'height' => 4,
                'weight' => 60,
                'sprites' => ['front_default' => 'https://raw.githubusercontent.com/.../25.png'],
                'types' => [['type' => ['name' => 'electric']]],
                'stats' => [['base_stat' => 35, 'stat' => ['name' => 'hp']]] // Pikachu com 35 HP
            ], 200),
            "{$this->baseUrl}/bulbasaur" => Http::response([
                'name' => 'bulbasaur',
                'height' => 7,
                'weight' => 69,
                'sprites' => ['front_default' => 'https://raw.githubusercontent.com/.../1.png'],
                'types' => [['type' => ['name' => 'grass']]],
                'stats' => [['base_stat' => 45, 'stat' => ['name' => 'hp']]]
            ], 200)
        ]);

        $pokeApiClient = $this->app->make(PokeApiClient::class);
        $battleService = new PokemonBattleService($pokeApiClient);

        $result = $battleService->runBattle('Pikachu', 'Bulbasaur');

        $this->assertInstanceOf(BattleResultDto::class, $result);
        $this->assertEquals('bulbasaur', $result->getWinner()->getName());
        $this->assertEquals('Bulbasaur venceu!', $result->getMessage());
    }

    /**
     * Testar o cenário de empate
     */
    public function test_pokemon_battle_tie(): void
    {
        Http::fake([
            "{$this->baseUrl}/pikachu" => Http::response([
                'name' => 'pikachu',
                'height' => 4,
                'weight' => 60,
                'sprites' => ['front_default' => '...'],
                'types' => [['type' => ['name' => 'electric']]],
                'stats' => [['base_stat' => 40, 'stat' => ['name' => 'hp']]]
            ], 200),
            "{$this->baseUrl}/ditto" => Http::response([
                'name' => 'ditto',
                'height' => 3,
                'weight' => 40,
                'sprites' => ['front_default' => '...'],
                'types' => [['type' => ['name' => 'normal']]],
                'stats' => [['base_stat' => 40, 'stat' => ['name' => 'hp']]]
            ], 200)
        ]);

        $pokeApiClient = $this->app->make(PokeApiClient::class);
        $battleService = new PokemonBattleService($pokeApiClient);

        $result = $battleService->runBattle('Pikachu', 'Ditto');

        $this->assertNull($result->getWinner());
        $this->assertEquals('Empate', $result->getMessage());
    }
}
