<?php

namespace App\Services;

use App\DTOs\PokemonDto;
use App\Exceptions\HttpBadRequestException;
use App\Exceptions\HttpNotFoundException;
use Exception;

class PokeApiClient
{

    private string $baseUrl = 'https://pokeapi.co/api/v2/pokemon';

    public function fetch(int $limit = 100, int $offset = 0): array
    {
        // Chamada a API externa
        $curl = curl_init("{$this->baseUrl}?limit={$limit}&offset={$offset}");
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode != 200) {
            throw new Exception('Falha na comunicação com a PokéAPI. Tente mais tarde.');
        }

        $data = json_decode($response, true);

        return $data['results'];
    }
    public function fetchPokemon(string $pokemonName): PokemonDto
    {

        // Torna nome do pokemon em minúscula [Requisito Funcional 1 / Detalhe Técnico 3]
        $pokemonNameNormalize = strtolower(trim($pokemonName));

        if (empty($pokemonNameNormalize)) {
            throw new HttpBadRequestException('O nome do Pokémon não pode ser vazio.');
        }

        // Chamada a API externa
        $curl = curl_init("{$this->baseUrl}/{$pokemonNameNormalize}");
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);

        // Valida se não houve erro com a chamada
        if (!$response) {
            throw new Exception('Falha na comunicação com a PokéAPI. Tente mais tarde.');
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode == 404) {
            throw new HttpNotFoundException("O Pokémon '{$pokemonName}' não existe.");
        }

        $data = json_decode($response, true);

        $dataProcessed = array_find($data['stats'], function ($stat) {
            // Busca o valor do HP (hit points) do Pokémon [Detalhe Técnico 5]
            if ($stat['stat']['name'] === 'hp') {
                return $stat['base_stat'];
            }
            return 0;
        });

        // Hit points (HP) do Pokémon [Requisito Funcional 3]
        $hp = $dataProcessed['base_stat'];

        // Retorna objeto 
        return new PokemonDto(
            $data['name'],
            $hp,
            $data['sprites']['front_default'],
            $data['types'][0]['type']['name'],
            $data['stats'][0]['base_stat'],
            $data['height'],
            $data['weight']
        );
    }
}
