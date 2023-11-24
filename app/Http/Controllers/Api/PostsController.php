<?php

namespace App\Http\Controllers\Api;

use App\Notifications\LikeNotification;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    //
    public function show()
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');
        $posts = Post::whereIn('user_id', $users)
            ->with(['user' => function ($query) {
                $query->with('profile');
            }, 'comments'])
            ->get();

        $suggests = User::whereNotIn('id', $users)
            ->where('id', '!=', auth()->user()->id)
            ->with('profile')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        foreach ($posts as $post) {
            $post->liked = auth()->user()->hasLiked($post); // Assuming this method exists in the package
            $post->likeCount = $post->likersCount(); // Assuming this method exists in the package
            $post->commentCount = $post->comments->count();
        }

        return response()->json([
            'posts' => $posts,
            'suggestions' => $suggests,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => 'required',
            'image' => ['required', 'image'],
        ]);
        $imagePath = (request('image')->store('uploads', 'public'));
        $image = \Intervention\Image\Facades\Image::make(public_path("storage/{$imagePath}"));
        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);
    }

    public function like(Post $post)
    {
        auth()->user()->like($post);
        $post->user->notify(new LikeNotification(auth()->user(), $post));
        return response()->json(['success' => 'Post liked successfully']);
    }
    public function unlike(Post $post)
    {
        auth()->user()->unlike($post);
        return response()->json(['success' => 'Post unliked successfully']);
    }
}
