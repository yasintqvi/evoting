<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        $position = Position::firstOrCreate([
            'title' => $request->input('title'),
        ], [
            'title' => $request->input('title')
        ]);

        return response()->json(['success' => true, 'position_id' => $position->id], Response::HTTP_OK);
    }
}
