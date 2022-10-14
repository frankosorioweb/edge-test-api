<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PacienteController extends Controller
{

  private $id            = null;
  private $nombres       = null;
  private $apellidos     = null;
  private $edad          = null;
  private $sexo          = null;
  private $documento     = null;
  private $tipo_sangre   = null;
  private $telefono      = null;
  private $correo        = null;
  private $direccion     = null;

  public function findOneByIdOrGetAll(Request $request)
  {
    // Contruimos la api de pacientes y realizamos la petición
    $pacientes_api = $this->get_pacientes_api($request);
    $response = Http::get($pacientes_api);

    // Verificamos si se está realizando una búsqueda por id de usuario
    $verify_find_one_by_id = $this->verify_find_one_by_id($request, $response);
    if ($verify_find_one_by_id instanceof JsonResponse) {
      return $verify_find_one_by_id;
    }

    return new JsonResponse($response->json());
  }

  /**
   * Verifica si se ha obtenido un usuario cuando se está realizando una búsqueda por id,
   * en caso de no encontrarlo se retorna una respuesta estructurada con el código 404 correspondiente,
   * en caso de encontrarlo, se retorna TRUE
   * 
   * @return JsonResponse|bool
   */
  private function verify_find_one_by_id($request, $response)
  {
    $has_paciente_id_param = !empty($request->id);
    $paciente_not_found = $has_paciente_id_param && empty($response->json());

    if ($paciente_not_found) {
      return new JsonResponse([
        "msg" => "Paciente no encontrado"
      ], Response::HTTP_NOT_FOUND);
    }

    return TRUE;
  }

  /**
   * Retorna la api de pacientes de JSONPLACEHOLDER, en función se si se desea buscar obtener
   * ltodos os pacientes o buscar uno en particular a partir de su respectivo id
   * 
   * @return String
   */
  private function get_pacientes_api(Request $request)
  {
    $pacientes_url = getenv('JSON_PLACE_HOLDER_URL') . '/pacientes/';
    $search_id = $request->id;

    return $pacientes_url . ($search_id ? "$search_id" : "");
  }
}
