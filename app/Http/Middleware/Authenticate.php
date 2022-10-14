<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Authenticate extends Middleware
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */
  protected function redirectTo($request)
  {
    if (!$request->expectsJson()) {
      return route('login');
    }
  }

  /**
   * Verificamos que el api_key se envía y el mismo sea válido
   * 
   * @return JsonResponse|null
   */
  public function handle($request, Closure $next, ...$guards)
  {
    $validate_token = $this->get_missing_or_invalid_token_response($request);
    if ($validate_token instanceof JsonResponse) {
      return $validate_token;
    }

    return $next($request);
  }

  /**
   * Permite retornar una respuesta estructurada en caso de que se pueda validar
   * correctamente el token de seguridad api_key que debe ser proporcionado en el header
   * En caso de que el token sea válido, se retorna TRUE
   * 
   * @return JsonResponse|boolean
   */
  private function get_missing_or_invalid_token_response($request)
  {
    $header_api_key = $request->header('api_key');
    $api_key = base64_encode(getenv('API_KEY'));

    if (!isset($header_api_key) || $header_api_key !== $api_key) {
      return new JsonResponse([
        "msg" => "El token de seguridad no se ha proporcionado o no es válido"
      ], Response::HTTP_UNAUTHORIZED);
    }

    return TRUE;
  }
}
