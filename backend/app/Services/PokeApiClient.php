<?php

namespace App\Services;

use App\DTOs\PokemonDto;
use App\Exceptions\HttpBadRequestException;
use App\Exceptions\HttpNotFoundException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Exception;

class PokeApiClient
{
    private string $baseUrl = 'https://pokeapi.co/api/v2/pokemon';

    /**
     * Busca um único Pokémon
     */
    public function fetchPokemon(string $pokemonName): PokemonDto
    {
        $name = $this->normalizeName($pokemonName);
        $response = Http::get("{$this->baseUrl}/{$name}");

        if ($response->status() === 404) {
            throw new HttpNotFoundException("O Pokémon '{$pokemonName}' não existe.");
        }

        if ($response->failed()) {
            throw new Exception('Falha na comunicação com a PokéAPI. Tente mais tarde.');
        }

        return $this->parseResponse($response->json());
    }

    /**
     * Busca em forma paginada
     */
    public function fetch(int $limit = 100, int $offset = 0): array
    {
        $response = Http::get($this->baseUrl, ['limit' => $limit, 'offset' => $offset]);

        if ($response->failed()) {
            throw new Exception('Falha na comunicação com a PokéAPI. Tente mais tarde.');
        }

        return $response->json()['results'] ?? [];
    }

    /**
     * Executa a busca concorrente de dois Pokémons
     */
    public function fetchConcurrent(string $pokemonName1, string $pokemonName2): array
    {
        $name1 = $this->normalizeName($pokemonName1);
        $name2 = $this->normalizeName($pokemonName2);

        $responses = Http::pool(function (Pool $pool) use ($name1, $name2) {
            return [
                $pool->as('p1')->get("{$this->baseUrl}/{$name1}"),
                $pool->as('p2')->get("{$this->baseUrl}/{$name2}"),
            ];
        });

        $this->validateResponse($responses['p1'], $pokemonName1);
        $this->validateResponse($responses['p2'], $pokemonName2);

        return [
            $this->parseResponse($responses['p1']->json()),
            $this->parseResponse($responses['p2']->json())
        ];
    }

    /**
     * Auxiliar para processar a resposta e transformar em PokemonDto
     */
    public function parseResponse(array $data): PokemonDto
    {
        $hp = collect($data['stats'] ?? [])->first(function ($stat) {
            return ($stat['stat']['name'] ?? '') === 'hp';
        })['base_stat'] ?? 0;

        return new PokemonDto(
            $data['name'] ?? 'unknown',
            $hp,
            $data['sprites']['front_default'] ?? '',
            $data['types'][0]['type']['name'] ?? 'unknown',
            $data['stats'][0]['base_stat'] ?? 0,
            $data['height'] ?? 0,
            $data['weight'] ?? 0
        );
    }

    /**
     * Normaliza e valida o nome do Pokémon antes da requisição
     */
    public function normalizeName(string $pokemonName): string
    {
        $pokemonNameNormalize = strtolower(trim($pokemonName));

        if (empty($pokemonNameNormalize)) {
            throw new HttpBadRequestException('O nome do Pokémon não pode ser vazio.');
        }

        return $pokemonNameNormalize;
    }

    private function validateResponse($response, string $originalName): void
    {
        if ($response->status() === 404) {
            throw new HttpNotFoundException("O Pokémon '{$originalName}' não existe.");
        }

        if ($response->failed()) {
            throw new Exception('Falha na comunicação com a PokéAPI. Tente mais tarde.');
        }
    }
}
