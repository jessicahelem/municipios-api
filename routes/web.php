<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MunicipiosController;



Route::get('/municipios/{uf}', [MunicipiosController::class, 'index']);

Route::get('/municipios', function () {
    return view('municipios');
});
