<?php

use App\Http\Controllers\PokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/api', [PokemonController::class, 'index']);
Route::get('/api/pokemon', [PokemonController::class, 'getPokemon']);
Route::get('/api/battle', [PokemonController::class, 'battle']);
Route::get('/api/pokemons', [PokemonController::class, 'list']);
