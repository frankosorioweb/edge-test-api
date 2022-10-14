<?php

use App\Http\Middleware\Authenticate;
use App\Http\Controllers\PacienteController;
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
Route::get('/generateToken', function (Request $request) {
  $api_key = base64_encode(getenv("API_KEY"));

  return new JsonResponse([
    "api_key" => $api_key
  ]);
});

/**
 * Este endpoint se encarga de manejar todas las peticiones [GET] e información
 * de los pacientes del hospital, se requiere deuna autenticación de tipo
 * API KEY, el valor del token debe ser proporcionado mediante la clave api_key.
 * 
 * @return JsonResponse
 */
Route::get('/paciente/{id?}', [
  PacienteController::class,
  'findOneByIdOrGetAll'
])->middleware(Authenticate::class);

/**
 * Este endpoint permite agregar un nuevo paciente, a partir de los datos enviados en el body del Request,
 * se requiere deuna autenticación de tipo API KEY, el valor del token
 * debe ser proporcionado mediante la clave api_key.
 * 
 * @return JsonResponse
 */
Route::post('/paciente', [
  PacienteController::class,
  'addNew'
])->middleware(Authenticate::class);

/**
 * Este endpoint permite modificar los datos de un paciente, a partir de los datos enviados en el body del Request,
 * se requiere deuna autenticación de tipo API KEY, el valor del token
 * debe ser proporcionado mediante la clave api_key.
 * 
 * @return JsonResponse
 */
Route::put('/paciente', [
  PacienteController::class,
  'update'
])->middleware(Authenticate::class);

/**
 * Este endpoint permite eliminar a un paciente, a partir de su id enviado como parémetro en el path
 * se requiere deuna autenticación de tipo API KEY, el valor del token
 * debe ser proporcionado mediante la clave api_key.
 * 
 * @return JsonResponse
 */
Route::delete('/paciente/{id?}', [
  PacienteController::class,
  'delete'
])->middleware(Authenticate::class);