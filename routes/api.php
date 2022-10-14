<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Este endpoint se utiliza para obtener el api_key que será necesario para
 * poder ejecutar cualquier otro servicio de esta API, para poder ejecutar
 * cualquier otro servicio, se debe enviar en el header la propiedad api_key
 * con el respectivo valor que retorna este endpoint
 * 
 * @param Request
 * @return JsonResponse
 */
Route::get('/generateToken', function(Request $request) {
  $api_key = base64_encode(getenv("API_KEY"));

  return new JsonResponse([
    "api_key" => $api_key
  ]);
});