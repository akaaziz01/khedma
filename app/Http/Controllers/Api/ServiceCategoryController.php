<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceCategoryRequest;
use App\Http\Requests\Admin\UpdateServiceCategoryRequest;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    /**
     * List all categories.
     * Public endpoint — no auth required (providers & clients use this when browsing/creating services).
     * Admins may pass ?include_inactive=true to also see deactivated categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceCategory::withCount('services')
            ->orderBy('sort_order')
            ->orderBy('name');

        $isAdmin = $request->user()?->isAdmin();

        if (! $isAdmin || ! $request->boolean('include_inactive')) {
            $query->where('is_active', true);
        }

        $categories = $query->get();

        return response()->json([
            'data' => ServiceCategoryResource::collection($categories),
        ]);
    }

    /**
     * Create a new service category (admin only).
     */
    public function store(StoreServiceCategoryRequest $request): JsonResponse
    {
        $category = ServiceCategory::create($request->validated());

        return response()->json([
            'message' => "Category \"{$category->name}\" created successfully.",
            'data'    => new ServiceCategoryResource($category->loadCount('services')),
        ], 201);
    }

    /**
     * Show a single category with its service count.
     */
    public function show(int $id): JsonResponse
    {
        $category = ServiceCategory::withCount('services')->findOrFail($id);

        return response()->json([
            'data' => new ServiceCategoryResource($category),
        ]);
    }

    /**
     * Update a category (admin only).
     */
    public function update(UpdateServiceCategoryRequest $request, int $id): JsonResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $category->update($request->validated());

        return response()->json([
            'message' => "Category \"{$category->name}\" updated successfully.",
            'data'    => new ServiceCategoryResource($category->loadCount('services')),
        ]);
    }

    /**
     * Soft-delete a category (admin only).
     * Blocked if the category still has approved services attached.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = ServiceCategory::withCount('services')->findOrFail($id);

        if ($category->hasActiveServices()) {
            return response()->json([
                'message' => "Cannot delete \"{$category->name}\": it still has approved services. "
                    . 'Reassign or reject those services first.',
            ], 422);
        }

        $name = $category->name;
        $category->delete(); // soft delete

        return response()->json([
            'message' => "Category \"{$name}\" has been removed.",
        ]);
    }
}

