<?php

namespace App\DTOs;

class PokemonExtraAttributesDto
{

    private readonly string $sprite;
    private readonly string $type;
    private readonly int $speed;
    private readonly int $height;
    private readonly int $weight;

    public function __construct(string $sprite, string $type, int $speed, int $height, int $weight)
    {
        $this->validate($sprite, $type, $speed, $height, $weight);
        $this->sprite = $sprite;
        $this->type = $type;
        $this->speed = $speed;
        $this->height = $height;
        $this->weight = $weight;
    }

    public function getSprite(): string
    {
        return $this->sprite;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    private function validate(string $sprite, string $type, int $speed, int $height, int $weight): void
    {
        if (empty($sprite)) {
            throw new \InvalidArgumentException("O campo sprite não pode ser vazio.");
        }

        if (empty($type)) {
            throw new \InvalidArgumentException("O campo type não pode ser vazio.");
        }

        if ($speed <= 0) {
            throw new \InvalidArgumentException("A velocidade do Pokémon deve ser um número positivo.");
        }

        if ($height <= 0) {
            throw new \InvalidArgumentException("A altura do Pokémon deve ser um número positivo.");
        }

        if ($weight <= 0) {
            throw new \InvalidArgumentException("O peso do Pokémon deve ser um número positivo.");
        }
    }
}
