<?php

namespace App\Http\Controllers\hr\hr1;

use App\Http\Controllers\Controller;
use App\Models\RecognitionPost;
use App\Models\RecognitionLike;
use App\Models\RecognitionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialRecognitionController extends Controller
{
    public function like(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $post = RecognitionPost::findOrFail($id);
        $userId = Auth::id();

        $like = RecognitionLike::where('post_id', $id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
            $status = 'unliked';
        } else {
            RecognitionLike::create([
                'post_id' => $id,
                'user_id' => $userId
            ]);
            $post->increment('likes_count');
            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'likes_count' => $post->likes_count
        ]);
    }

    public function comment(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'comment_text' => 'required|string|max:1000'
        ]);

        $post = RecognitionPost::findOrFail($id);
        
        $comment = RecognitionComment::create([
            'post_id' => $id,
            'user_id' => Auth::id(),
            'comment_text' => $request->comment_text
        ]);

        $post->increment('comments_count');

        return response()->json([
            'status' => 'success',
            'comment' => [
                'username' => Auth::user()->username,
                'text' => $comment->comment_text,
                'date' => $comment->created_at->diffForHumans()
            ],
            'comments_count' => $post->comments_count
        ]);
    }
}
