<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('isActiveRoute')) {
  function isActiveRoute($route, $output = 'active')
  {
    if (Route::currentRouteName() == $route) {
      return $output;
    }
  }
}
