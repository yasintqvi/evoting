<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => ['required', 'string', 'min:2', 'max:255'],
            ]);

            $position = Position::firstOrCreate([
                'title' => $request->input('title'),
            ], [
                'title' => $request->input('title')
            ]);

            return response()->json(['success' => true, 'position_id' => $position->id], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error while creating position', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('messages.position.error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
