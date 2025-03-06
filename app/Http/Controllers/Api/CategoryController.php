<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryRequest;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepository->all();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $category = $this->categoryRepository->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = $this->categoryRepository->find($id);

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = $this->categoryRepository->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $this->categoryRepository->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully'
        ]);
    }
}