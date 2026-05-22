<?php

namespace App\Services;

use App\DTOs\PokemonDto;
use Exception;

class PokeApiClient
{

    private string $baseUrl = 'https://pokeapi.co/api/v2/pokemon';

    public function fetchPokemon(string $pokemonName): PokemonDto
    {

        // Torna nome do pokemon em minúscula [Requisito Funcional 1 / Detalhe Técnico 3]
        $pokemonNameNormalize = strtolower(trim($pokemonName));

        if (empty($pokemonNameNormalize)) {
            throw new Exception('O nome do Pokémon não pode ser vazio.');
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

        // Verivida status code do retorno
        if ($httpCode == 404) {
            throw new Exception("O Pokémon '{$pokemonName}' não existe.");
        }

        $data = json_decode($response, true);
        // // Teste local com arquivo JSON
        // $data = file_get_contents(__DIR__ .  "/pokemon.json");
        // $data = json_decode($data, true);

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
