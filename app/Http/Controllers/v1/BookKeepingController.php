<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookKeeping\Store;
use App\Http\Requests\BookKeeping\Update;
use App\Http\Resources\BookKeepingCollection;
use App\Services\BookKeepingService;
use Exception;
use Illuminate\Http\Request;

class BookKeepingController extends Controller
{
    protected $bookKeepingService;

    public function __construct(BookKeepingService $bookKeepingService)
    {
        $this->bookKeepingService = $bookKeepingService;
    }

    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $result = $this->bookKeepingService->all($params);

            return new BookKeepingCollection($result);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch book keeping data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Store $request)
    {
        try {
            $result = $this->bookKeepingService->store($request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $result,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create book keeping',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(string $id, Update $request)
    {
        try {
            $result = $this->bookKeepingService->update($id, $request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update book keeping',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(string $id)
    {
        try {
            $result = $this->bookKeepingService->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Book keeping deleted successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete book keeping',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
