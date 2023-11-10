<?php

namespace App\Http\Controllers;

use App\Services\HashService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HashController extends Controller
{
    private HashService $hashService;

    public function __construct(HashService $hashService)
    {
        $this->hashService = $hashService;
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->json()->all();
        if (empty($data)) {
            return response()->json(['error' => 'Data is required'], 400);
        }

        try {
            $result = $this->hashService->store($data);
            return response()->json($result, 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function read($hash): JsonResponse
    {
        if (empty($hash)) {
            return response()->json(['error' => 'Hash is required'], 400);
        }

        try {
            $result = $this->hashService->read($hash);
            return response()->json($result);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
