<?php

namespace App\Http\Controllers\Api;

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
        $liked = [];
        $likeCount = [];
        foreach ($posts as $post) {
            $liked[$post->id] = auth()->user()->hasLiked($post);
            $likeCount[$post->id] = $post->likersCount();
        }

        return [
            'posts' => $posts,
            'suggestions' => $suggests,
            'liked' => $liked,
            'likeCount' => $likeCount
        ];
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
}
