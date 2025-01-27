<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\BookKeepingService;

class BookKeepingController extends Controller
{
    protected $bookKeepingService;

    public function __construct(BookKeepingService $bookKeepingService)
    {
        $this->bookKeepingService = $bookKeepingService;
    }

    public function all(array $params)
    {
        $result = $this->bookKeepingService->all($params);

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function store(array $attributes)
    {
        $result = $this->bookKeepingService->store($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function update(string $id, array $attributes)
    {
        $result = $this->bookKeepingService->update($id, $attributes);

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function delete(string $id)
    {
        $result = $this->bookKeepingService->delete($id);

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }
}
