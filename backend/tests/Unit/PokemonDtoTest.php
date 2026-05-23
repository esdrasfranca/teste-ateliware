<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\DTOs\PokemonDto;


class PokemonDtoTest extends TestCase
{

    private string $name = 'Pikachu';
    private int $hp = 35;

    private string $sprite = 'https://example.com/sprite.png';

    private string $type = 'Electric';
    private int $speed = 90;
    private int $height = 4;
    private int $weight = 6;

    public function test_pokemon_dto_creation()
    {
        $pokemonDto = new PokemonDto(
            $this->name,
            $this->hp,
            $this->sprite,
            $this->type,
            $this->speed,
            $this->height,
            $this->weight
        );

        // Valida se os valores foram atribuídos corretamente
        $this->assertEquals($this->name, $pokemonDto->getName());
        $this->assertEquals($this->hp, $pokemonDto->getHp());
        $this->assertEquals($this->sprite, $pokemonDto->getSprite());
        $this->assertEquals($this->type, $pokemonDto->getType());
        $this->assertEquals($this->speed, $pokemonDto->getSpeed());
        $this->assertEquals($this->height, $pokemonDto->getHeight());
        $this->assertEquals($this->weight, $pokemonDto->getWeight());
    }

    public function test_pokemon_dto_invalid_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            '',
            $this->hp,
            $this->sprite,
            $this->type,
            $this->speed,
            $this->height,
            $this->weight
        );
    }

    public function test_pokemon_dto_invalid_hp()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            $this->name,
            -10,
            $this->sprite,
            $this->type,
            $this->speed,
            $this->height,
            $this->weight
        );
    }

    public function test_pokemon_dto_invalid_sprite()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            $this->name,
            $this->hp,
            '',
            $this->type,
            $this->speed,
            $this->height,
            $this->weight
        );
    }

    public function test_pokemon_dto_invalid_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            $this->name,
            $this->hp,
            $this->sprite,
            '',
            $this->speed,
            $this->height,
            $this->weight
        );
    }

    public function test_pokemon_dto_invalid_speed()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            $this->name,
            $this->hp,
            $this->sprite,
            $this->type,
            -10,
            $this->height,
            $this->weight
        );
    }

    public function test_pokemon_dto_invalid_height()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            $this->name,
            $this->hp,
            $this->sprite,
            $this->type,
            $this->speed,
            -10,
            $this->weight
        );
    }

    public function test_pokemon_dto_invalid_weight()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PokemonDto(
            $this->name,
            $this->hp,
            $this->sprite,
            $this->type,
            $this->speed,
            $this->height,
            -10
        );
    }

    public function test_pokemon_dto_to_array()
    {
        $pokemonDto = new PokemonDto(
            $this->name,
            $this->hp,
            $this->sprite,
            $this->type,
            $this->speed,
            $this->height,
            $this->weight
        );

        $result = $pokemonDto->toArray();

        // Valida se o array está no formato esperado
        $this->assertIsArray($result);
        $this->assertEquals($this->name, $result['name']);
        $this->assertEquals($this->hp, $result['hitPoint']);
        $this->assertEquals($this->sprite, $result['sprite']);
        $this->assertEquals($this->type, $result['type']);
        $this->assertEquals($this->speed, $result['speed']);
        $this->assertEquals($this->height, $result['height']);
        $this->assertEquals($this->weight, $result['weight']);
    }
}
