<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @method \Illuminate\Auth\Access\Response authorize(string $ability, mixed $arguments = [])
 */
abstract class Controller
{
    use AuthorizesRequests;
}
