<?php

namespace App\DTOs;

class BattleResultDto
{

    private readonly PokemonDto $pokemon1;
    private readonly PokemonDto $pokemon2;
    private readonly string $winner;
    private readonly string $message;

    public function __construct(PokemonDto $pokemon1, PokemonDto $pokemon2, ?string $winner, string $message)
    {
        $this->validate($message);
        $this->pokemon1 = $pokemon1;
        $this->pokemon2 = $pokemon2;
        $this->winner = $winner;
        $this->message = $message;
    }

    public function getPokemon1(): PokemonDto
    {
        return $this->pokemon1;
    }

    public function getPokemon2(): PokemonDto
    {
        return $this->pokemon2;
    }

    public function getWinner(): string
    {
        return $this->winner;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    private function validate(string $message): void
    {

        // if (empty($winner)) {
        //     throw new \InvalidArgumentException('O nome do vencedor não pode ser vazio.');
        // }

        if (empty($message)) {
            throw new \InvalidArgumentException('A mensagem de resultado não pode ser vazia.');
        }
    }
}
