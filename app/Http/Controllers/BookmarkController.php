<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark status untuk post
     */
    public function toggle(Post $post): JsonResponse
    {
        $user = auth()->user();

        // Cek jika post sudah dibookmark
        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($bookmark) {
            // Hapus bookmark jika sudah ada
            $bookmark->delete();
            $message = 'Post telah dihapus dari bookmark';
            $isBookmarked = false;
        } else {
            // Tambahkan bookmark jika belum ada
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id
            ]);
            $message = 'Post telah ditambahkan ke bookmark';
            $isBookmarked = true;
        }

        return response()->json([
            'message' => $message,
            'is_bookmarked' => $isBookmarked
        ]);
    }

    /**
     * Mendapatkan semua post yang dibookmark oleh user
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $bookmarkedPosts = $user->bookmarkedPosts()
            ->with(['category', 'media'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'bookmarks' => $bookmarkedPosts
        ]);
    }

    /**
     * Cek status bookmark untuk post tertentu
     */
    public function check(Post $post): JsonResponse
    {
        $user = auth()->user();
        $isBookmarked = $user->hasBookmarked($post);

        return response()->json([
            'is_bookmarked' => $isBookmarked
        ]);
    }
}