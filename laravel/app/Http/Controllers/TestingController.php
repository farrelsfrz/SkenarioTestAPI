<?php

namespace App\Http\Controllers;

use App\Models\TestStep;
use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function index()
    {
        // Mengambil semua test steps dengan relasi skenario
        $testSteps = Testing::with('skenario')->get();
        return response()->json($testSteps);
    }

    public function show(string $id)
    {
        // Menampilkan test step berdasarkan ID
        $testStep = TestStep::find($id);
        if (!$testStep) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($testStep);
    }

    public function store(Request $request)
    {
        // Menyimpan test step baru
        $testStep = TestStep::create($request->all());
        return response()->json($testStep, 201);
    }

    public function update(Request $request, string $id)
    {
        // Memperbarui test step
        $testStep = TestStep::find($id);
        if (!$testStep) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $testStep->update($request->all());
        return response()->json($testStep);
    }

    public function destroy($id)
    {
        $testStep = TestStep::find($id);
        if (!$testStep) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $testStep->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}