<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use App\Models\RecognitionPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminSocialRecognitionController extends Controller
{
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHr1Admin();
        $posts = RecognitionPost::with('admin')->orderBy('created_at', 'desc')->get();
        
        // Metrics
        $totalPosts = $posts->count();
        $totalLikes = $posts->sum('likes_count');
        $totalComments = $posts->sum('comments_count');
        $engagementRate = $totalPosts > 0 ? round(($totalLikes + $totalComments) / $totalPosts, 1) : 0;

        return view('admin.hr1.social recognition.index', compact('posts', 'totalPosts', 'totalLikes', 'totalComments', 'engagementRate'));
    }

    public function create()
    {
        $this->authorizeHr1Admin();
        return view('admin.hr1.social recognition.create');
    }

    public function store(Request $request)
    {
        $this->authorizeHr1Admin();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recognition', 'public');
        }

        RecognitionPost::create([
            'admin_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.hr1.recognition.index')->with('success', 'Recognition post created successfully.');
    }

    public function edit($id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::findOrFail($id);
        return view('admin.hr1.social recognition.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $post->image_path = $request->file('image')->store('recognition', 'public');
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.hr1.recognition.index')->with('success', 'Recognition post updated successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeHr1Admin();
        $post = RecognitionPost::findOrFail($id);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()->route('admin.hr1.recognition.index')->with('success', 'Recognition post deleted successfully.');
    }
}
