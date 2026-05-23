<?php

namespace App\Http\Controllers;

use App\Exceptions\HttpBadRequestException;
use App\Exceptions\HttpNotFoundException;
use App\Services\PokeApiClient;
use App\Services\PokemonBattleService;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class PokemonController
{

    private PokeApiClient $pokeApiClient;

    public function __construct()
    {
        $this->pokeApiClient = new PokeApiClient();
    }

    public function index()
    {
        // Descrição da API exibida na entrada do método index
        echo "Bem-vindo à API Pokédex: consulte detalhes de Pokémon e realize batalhas simuladas usando endpoints REST.";
        echo "\n";
        echo "\n";
        echo "Endpoints disponíveis:";
        echo "\n";
        echo "- api/pokemon?name=[NOME_DO_POKEMON]";
        echo "\n";
        echo "- api/battle?pokemon1=[NOME_DO_POKEMON_1]&pokemon2=[NOME_DO_POKEMON_2]";
        echo "\n";
    }

    public function getPokemon(Request $request, Response $response)
    {
        try {
            $pokemonName = $request->query('name');

            if (empty(trim($pokemonName))) {
                throw new HttpBadRequestException('O nome do Pokémon não pode ser vazio.');
            }

            $pokemon = $this->pokeApiClient->fetchPokemon($pokemonName);

            return response([
                'data' => $pokemon->toArray(),
                'status' => 'success'
            ], 200)
                ->header('Content-Type', 'application/json');
        } catch (HttpBadRequestException $e) {
            return response([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 400)
                ->header('Content-Type', 'application/json');
        } catch (HttpNotFoundException $e) {
            return response([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 404)
                ->header('Content-Type', 'application/json');
        } catch (\Throwable $th) {
            return response([
                'message' => "Resposta inválida da PokeAPI. Tente novamente mais tarde.",
                'status' => 'error'
            ], 502)
                ->header('Content-Type', 'application/json');
        }
    }

    public function battle(Request $request, Response $response)
    {

        try {
            $pokemon1 = $request->query('pokemon1');
            $pokemon2 = $request->query('pokemon2');

            if (empty(trim($pokemon1)) || empty(trim($pokemon2))) {
                throw new HttpBadRequestException('Os nomes dos Pokémon não podem ser vazios.');
            }

            $battle = new PokemonBattleService($pokemon1, $pokemon2);
            $result = $battle->runBattle();

            return response([
                'data' => [
                    'pokemon1' => $result->getPokemon1()->toArray(),
                    'pokemon2' => $result->getPokemon2()->toArray(),
                    'winner' => $result->getWinner(),
                    'message' => $result->getMessage()
                ],
                'status' => 'success'
            ], 200)
                ->header('Content-Type', 'application/json');
        } catch (HttpBadRequestException $e) {
            return response([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 400)
                ->header('Content-Type', 'application/json');
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => 'error'
            ], 500)
                ->header('Content-Type', 'application/json');
        }
    }
}
