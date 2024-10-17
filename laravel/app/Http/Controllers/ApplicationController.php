<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApplicationController extends Controller
{
    public function index()
    {
        // Mengambil data dari backend Node.js
        $response = Http::get('http://172.31.202.205:3000/applications');
        return response()->json($response->json());
    }
}
