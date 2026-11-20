<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function categories(): JsonResponse
    {
        return response()->json(
            Category::orderBy('name')->get(['id', 'name'])
        );
    }

    public function departments(): JsonResponse
    {
        return response()->json(
            Department::orderBy('name')->get(['id', 'name', 'code'])
        );
    }
}
