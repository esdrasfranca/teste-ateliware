<?php

namespace App\DTOs;

/**
 * Data Transfer Object (DTO) para representar um Pokémon.
 */
class PokemonDto
{

    private readonly string $name;
    private readonly int $hitPoint; // Hit Points
    private readonly string $sprite;
    private readonly string $type;
    private readonly int $speed;
    private readonly int $height;
    private readonly int $weight;


    public function __construct(string $name, int $hitPoint, string $sprite, string $type, int $speed, int $height, int $weight)
    {
        $this->validate($name, $hitPoint, $sprite, $type, $speed, $height, $weight);
        $this->name = $name;
        $this->hitPoint = $hitPoint;
        $this->sprite = $sprite;
        $this->type = $type;
        $this->speed = $speed;
        $this->height = $height;
        $this->weight = $weight;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHp(): int
    {
        return $this->hitPoint;
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

    public function toArray(): array
    {
        return array(
            'name' => $this->name,
            'hitPoint' => $this->hitPoint,
            'sprite' => $this->sprite,
            'type' => $this->type,
            'speed' => $this->speed,
            'height' => $this->height,
            'weight' => $this->weight
        );
    }

    private function validate(string $name, int $hitPoint, string $sprite, string $type, int $speed, int $height, int $weight): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("O nome do Pokémon não pode ser vazio.");
        }

        if ($hitPoint < 0) {
            throw new \InvalidArgumentException("O Hit Point do Pokémon deve ser um número positivo.");
        }

        if (empty($sprite)) {
            throw new \InvalidArgumentException("O sprite do Pokémon não pode ser vazio.");
        }

        if (empty($type)) {
            throw new \InvalidArgumentException("O tipo do Pokémon não pode ser vazio.");
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
