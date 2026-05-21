<?php

namespace App\DTOs;

/**
 * Data Transfer Object (DTO) para representar o resultado de uma batalha Pokémon.
 */

use App\DTOs\PokemonDto;
use PHPUnit\Framework\TestCase;
use App\DTOs\BattleResultDto;

class BattleResultDtoTest extends TestCase
{

    private string $name1 = 'Pikachu';
    private int $hp1 = 35;
    private string $sprite1 = 'https://example.com/sprite.png';
    private string $type1 = 'Electric';
    private int $speed1 = 90;
    private int $height1 = 4;
    private int $weight1 = 6;

    private string $name2 = 'Bulbasaur';
    private int $hp2 = 45;
    private string $sprite2 = 'https://example.com/sprite2.png';
    private string $type2 = 'Grass';
    private int $speed2 = 45;
    private int $height2 = 7;
    private int $weight2 = 69;

    private string $winner = 'Pikachu';
    private string $message = 'Pikachu vence!';


    public function test_battle_result_dto_creation()
    {
        $pokemon1 = new PokemonDto(
            $this->name1,
            $this->hp1,
            $this->sprite1,
            $this->type1,
            $this->speed1,
            $this->height1,
            $this->weight1
        );
        $pokemon2 = new PokemonDto(
            $this->name2,
            $this->hp2,
            $this->sprite2,
            $this->type2,
            $this->speed2,
            $this->height2,
            $this->weight2
        );
        $winner = 'Pikachu';
        $message = 'Pikachu vence!';

        $battleResultDto = new BattleResultDto($pokemon1, $pokemon2, $winner, $message);

        // Valida se os valores foram atribuídos corretamente
        $this->assertEquals($pokemon1, $battleResultDto->getPokemon1());
        $this->assertEquals($pokemon2, $battleResultDto->getPokemon2());
        $this->assertEquals($winner, $battleResultDto->getWinner());
        $this->assertEquals($message, $battleResultDto->getMessage());
    }
}
