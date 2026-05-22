<?php

namespace Tests\Unit;

use App\DTOs\BattleResultDto;
// use App\DTOs\PokemonDto;
use App\Services\PokemonBattleService;
use PHPUnit\Framework\TestCase;

class PokemonBattleTest extends TestCase
{

    public function test_pokemon_battle()
    {
        $battle = new PokemonBattleService(
            'Pikachu',
            'Bulbasaur'
        );

        $result = $battle->runBattle();

        $this->assertInstanceOf(BattleResultDto::class, $result);
        $this->assertEquals('bulbasaur', $result->getWinner());
        $this->assertEquals('Bulbasaur venceu!', $result->getMessage());
    }

    // Testando empate
    public function test_pokemon_battle_tie()
    {
        $battle = new PokemonBattleService(
            'Pikachu',
            'Pikachu'
        );

        $result = $battle->runBattle();

        $this->assertEmpty($result->getWinner());

        $this->assertEquals('Empate', $result->getMessage());
    }
}
