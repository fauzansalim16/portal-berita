<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostImageRequest;
use App\Http\Requests\Api\PostRequest;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index()
    {
        $posts = $this->postRepository->all();

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function published()
    {
        $posts = $this->postRepository->published();

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function byCategory($categoryId)
    {
        $posts = $this->postRepository->findByCategory($categoryId);

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function store(PostRequest $request)
    {
        // Tambahkan user_id dari user yang terautentikasi
        $data = array_merge($request->validated(), [
            'user_id' => auth()->id()
        ]);

        $post = $this->postRepository->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    public function show($id)
    {
        $post = $this->postRepository->find($id);

        // Tambahkan URLs media
        $post->featured_image = $post->getFirstMediaUrl('featured_image');
        $post->gallery = $post->getMedia('gallery')->map(function ($media) {
            return $media->getUrl();
        });

        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    public function update(PostRequest $request, $id)
    {
        $post = $this->postRepository->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'data' => $post
        ]);
    }

    public function destroy($id)
    {
        $this->postRepository->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully'
        ]);
    }

    public function uploadFeaturedImage(PostImageRequest $request, $id)
    {
        $media = $this->postRepository->uploadFeaturedImage($id, $request->file('image'));

        return response()->json([
            'status' => 'success',
            'message' => 'Featured image uploaded successfully',
            'data' => [
                'url' => $media->getUrl()
            ]
        ]);
    }

    public function uploadGalleryImages(PostImageRequest $request, $id)
    {
        $mediaItems = $this->postRepository->uploadGalleryImages($id, $request->file('images'));

        $urls = collect($mediaItems)->map(function ($media) {
            return $media->getUrl();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Gallery images uploaded successfully',
            'data' => [
                'urls' => $urls
            ]
        ]);
    }
}