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

    // app/Http/Controllers/Api/PostController.php

    public function show($id)
    {
        $post = $this->postRepository->find($id);

        // Menambahkan URL media ke response
        $response = $post->toArray();
        $response['featured_image'] = $post->getFirstMediaUrl('featured_image');
        $response['gallery'] = $post->getMedia('gallery')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'file_name' => $media->file_name
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    // Tambahkan juga di method index dan lainnya yang menampilkan post

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

    // app/Http/Controllers/Api/PostController.php

    // Metode untuk upload Featured Image
    public function uploadFeaturedImage(PostImageRequest $request, $id)
    {
        $post = $this->postRepository->find($id);

        // Hapus gambar lama jika ada
        if ($post->hasMedia('featured_image')) {
            $post->clearMediaCollection('featured_image');
        }

        // Upload gambar baru
        $media = $post->addMedia($request->file('image'))
            ->toMediaCollection('featured_image');

        return response()->json([
            'status' => 'success',
            'message' => 'Featured image uploaded successfully',
            'data' => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'file_name' => $media->file_name
            ]
        ]);
    }

    // Metode untuk upload Gallery Images
    public function uploadGalleryImages(PostImageRequest $request, $id)
    {
        $post = $this->postRepository->find($id);
        $mediaItems = [];

        foreach ($request->file('images') as $image) {
            $media = $post->addMedia($image)
                ->toMediaCollection('gallery');

            $mediaItems[] = [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'file_name' => $media->file_name
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Gallery images uploaded successfully',
            'data' => $mediaItems
        ]);
    }

    // Metode untuk menghapus media
    public function deleteMedia($id, $mediaId)
    {
        $post = $this->postRepository->find($id);
        $media = $post->media()->findOrFail($mediaId);
        $media->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Media deleted successfully'
        ]);
    }
}