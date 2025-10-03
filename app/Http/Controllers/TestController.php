<?php

namespace App\Http\Controllers;

use Celovel\Http\Controller;
use Celovel\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        return [
            'message' => 'Hello from TestController!',
            'method' => $request->getMethod(),
            'path' => $request->getPath()
        ];
    }
}
