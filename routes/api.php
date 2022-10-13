<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/generateToken', function(Request $request) {
  return new JsonResponse([
    "msg" => "Hello from /generateToken [GET]"
  ]);
});