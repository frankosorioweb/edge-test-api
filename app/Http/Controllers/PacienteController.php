<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PacienteController extends Controller
{

  private $id          = null;
  private $nombres     = null;
  private $apellidos   = null;
  private $edad        = null;
  private $sexo        = null;
  private $documento   = null;
  private $tipo_sangre = null;
  private $telefono    = null;
  private $correo      = null;
  private $direccion   = null;

  /**
   * El endpoint /paciente/{id?} [GET] se utiliza para comunicarse con esta función y actuar de controlador
   * 
   * @return JsonResponse
   */
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
   * El endpoint /paciente [POST] se utiliza para comunicarse con esta función y actuar de controlador
   * 
   * @return JsonResponse
   */
  public function addNew(Request $request)
  {
    // Verificamos si los parámetros del nuevo paciente son válidos
    $verify_parameters = $this->verify_add_new_paciente_parameters($request);
    if ($verify_parameters instanceof JsonResponse) {
      return $verify_parameters;
    }

    // Construimos la url de la api correspondiente para insertar un nuevo paciente
    $pacientes_post_api = $this->get_pacientes_api();
    $paciente_http_body_parameters = $this->get_paciente_http_body($request);

    // Enviamos la petición [POST] a JSONPLACEHOLDER y obtenemos el response
    $response = $paciente_http_body_parameters->post($pacientes_post_api);

    return new JsonResponse(
      $response->json(),
      Response::HTTP_CREATED
    );
  }


  /**
   * El endpoint /paciente [PUT] se utiliza para comunicarse con esta función y actuar de controlador
   * 
   * @return JsonResponse
   */
  public function update(Request $request)
  {
    // Verificamos si los parámetros del nuevo paciente son válidos
    $verify_parameters = $this->verify_add_new_paciente_parameters($request);
    if ($verify_parameters instanceof JsonResponse) {
      return $verify_parameters;
    }

    // Construimos la url de la api correspondiente para modificar los datos de un paciente
    $paciente_id = $request->id;
    $pacientes_api = $this->get_pacientes_api();
    $pacientes_api.= $paciente_id;

    // Verificamos si el paciente a modificar existe mediante su id
    $paciente_to_update = Http::get($pacientes_api);
    if(empty($paciente_to_update->json())) {
      return $this->get_paciente_not_found_response();
    }
    
    // Enviamos la petición [PUT] a JSONPLACEHOLDER y obtenemos el response
    $paciente_http_body_parameters = $this->get_paciente_http_body($request);
    $response = $paciente_http_body_parameters->put($pacientes_api);

    return new JsonResponse($response->json());
  }

  /**
   * El endpoint /paciente [DELETE] se utiliza para comunicarse con esta función y actuar de controlador
   * 
   * @return JsonResponse
   */
  public function delete(Request $request)
  {
    return new JsonResponse([
      "msg" => "Hello from /paciente [DELETE]"
    ]);
  }

  private function get_paciente_http_body(Request $request)
  {
    return Http::withBody(json_encode([
      "id" => intval($request->get('id')),
      "nombres" => $request->get('nombres'),
      "apellidos" => $request->get('apellidos'),
      "edad" => intval($request->get('edad')),
      "sexo" => $request->get('sexo'),
      "dni" => intval($request->get('dni')),
      "tipo_sangre" => $request->get('tipo_sangre'),
      "telefono" => intval($request->get('telefono')),
      "correo" => $request->get('correo'),
      "direccion" => $request->get('direccion')
    ]), "application/json");
  }

  /**
   * Verifica si se ha obtenido un usuario cuando se está realizando una búsqueda por id,
   * en caso de no encontrarlo se retorna una respuesta estructurada con el código 404 correspondiente,
   * en caso de encontrarlo, se retorna TRUE
   * 
   * @return JsonResponse|bool
   */
  private function verify_find_one_by_id(Request $request, $response)
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
   * Retorna la api de pacientes de JSONPLACEHOLDER, permite construir la api en función se si se desea obtener
   * todos los pacientes, buscar uno en particular a partir de su respectivo id o simplemente retorna la api original
   * cuando el parámetro request es null
   * 
   * @return String
   */
  private function get_pacientes_api(Request $request = null)
  {
    $pacientes_url = getenv('JSON_PLACE_HOLDER_URL') . '/pacientes/';

    if (!isset($request)) {
      return $pacientes_url;
    }

    $search_id = $request->id;

    return $pacientes_url . ($search_id ? "$search_id" : "");
  }

  /**
   * Verifica si se han enviado correctamente los datos del usuario que se desea agregar,
   * retorna una respuesta estructurada con el código 400 cuando no se envían los datos de manera
   * adecuada o TRUE en caso de validar correctamente los datos
   * 
   * @return JsonResponse|bool
   */
  private function verify_add_new_paciente_parameters(Request $request)
  {
    $parameters = $request->all();
    if (empty($parameters) || empty($request->id)) {
      return $this->get_invalid_paciente_parameters_response();
    }

    return TRUE;
  }

  /**
   * Retorna una respuesta estructurada en formato JsonResponse, se utiliza como
   * respuesta cuando los datos enviados del paciente son inválidos
   * 
   * @return JsonResponse
   */
  private function get_invalid_paciente_parameters_response()
  {
    return new JsonResponse([
      "msg" => "Los datos del paciente son incorrectos o no se han proporcionado"
    ], Response::HTTP_BAD_REQUEST);
  }

  function get_paciente_not_found_response() {
    return new JsonResponse([
      "msg" => "Paciente no encontrado"
    ], Response::HTTP_NOT_FOUND);
  }
}
