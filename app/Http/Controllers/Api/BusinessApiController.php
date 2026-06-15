<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\JsonResponse;

class BusinessApiController extends Controller
{
    /**
     * Zwraca listę zatwierdzonych lokali (REST API).
     */
    public function index(): JsonResponse
    {
        $businesses = Business::with(['owner', 'services', 'photos'])
            ->where('is_approved', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $businesses
        ], 200);
    }

    /**
     * Zwraca szczegóły pojedynczego lokalu (REST API).
     */
    public function show(int $id): JsonResponse
    {
        $business = Business::with(['owner', 'services', 'photos'])
            ->where('is_approved', true)
            ->find($id);

        if (!$business) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nie znaleziono lokalu'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $business
        ], 200);
    }
}
