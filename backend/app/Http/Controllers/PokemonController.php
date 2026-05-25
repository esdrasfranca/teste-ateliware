<?php

namespace App\Http\Controllers;

use App\Exceptions\HttpBadRequestException;
use App\Exceptions\HttpNotFoundException;
use App\Services\PokeApiClient;
use App\Services\PokemonBattleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class PokemonController
{
    private int $limit = 100;

    public function __construct(
        private readonly PokeApiClient $pokeApiClient,
        private readonly PokemonBattleService $pokemonBattleService
    ) {}

    public function list(Request $request): JsonResponse
    {
        try {
            $page = preg_replace('/[^0-9]/', '', $request->query('page'));

            if (empty($page)) {
                $page = 0;
            }

            $result = $this->pokeApiClient->fetch($this->limit, $page * $this->limit);

            return response()->json([
                'data' => array_map(function ($item) {
                    return [
                        "name" => $item['name'],
                        "url" => url('api/pokemon?name=' . $item['name'])
                    ];
                }, $result),
                'count' => count($result),
                'next' => url()->current() . '?page=' . ($page + 1),
                'previous' => $page == 0 ? null : url()->current() . '?page=' . ($page - 1),
                'status' => 'success'
            ], 200);
        } catch (HttpBadRequestException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 400);
        } catch (Throwable $th) {
            return response()->json([
                'message' => "Ocorreu um erro ao processar a solicitação. Tente novamente mais tarde.",
                'status' => 'error'
            ], 500);
        }
    }

    public function getPokemon(Request $request): JsonResponse
    {
        try {
            $pokemonName = $request->query('name');

            if (empty(trim($pokemonName))) {
                throw new HttpBadRequestException('O nome do Pokémon não pode ser vazio.');
            }

            $pokemon = $this->pokeApiClient->fetchPokemon($pokemonName);

            return response()->json([
                'data' => $pokemon->toArray(),
                'status' => 'success'
            ], 200);
        } catch (HttpBadRequestException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 400);
        } catch (HttpNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 404);
        } catch (Throwable $th) {
            return response()->json([
                'message' => "Resposta inválida da PokeAPI. Tente novamente mais tarde.",
                'status' => 'error'
            ], 502);
        }
    }

    public function battle(Request $request): JsonResponse
    {
        try {
            $pokemon1 = $request->query('pokemon1');
            $pokemon2 = $request->query('pokemon2');

            if (empty(trim($pokemon1)) || empty(trim($pokemon2))) {
                throw new HttpBadRequestException('Os nomes dos Pokémon não podem ser vazios.');
            }

            $result = $this->pokemonBattleService->runBattle($pokemon1, $pokemon2);

            return response()->json([
                'data' => [
                    'pokemon1' => $result->getPokemon1()->toArray(),
                    'pokemon2' => $result->getPokemon2()->toArray(),
                    'winner' => $result->getWinner() ? $result->getWinner()->toArray() : null,
                    'message' => $result->getMessage()
                ],
                'status' => 'success'
            ], 200);
        } catch (HttpBadRequestException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 400);
        } catch (HttpNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 404);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
