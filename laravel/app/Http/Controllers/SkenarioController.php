<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use Illuminate\Http\Request;

class SkenarioController extends Controller
{
    public function index()
    {
        $testCases = TestCase::all();
        return response()->json($testCases);
    }

    public function show(string $id)
    {
        \Log::info("Fetching test case with ID: $id");
        $testCase = TestCase::find($id);
        if (!$testCase) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($testCase);
    }

    public function store(Request $request)
    {
        $request->validate([
            'application_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|integer',
        ]);

        $testCase = TestCase::create($request->all());
        return response()->json($testCase, 201);
    }

    public function update(Request $request, string $id)
    {
        $testCase = TestCase::find($id);
        if (!$testCase) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'application_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|integer',
        ]);

        $testCase->update($request->all());
        return response()->json($testCase);
    }

    public function destroy($id)
    {
        $testCase = TestCase::find($id);
        if (!$testCase) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $testCase->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}