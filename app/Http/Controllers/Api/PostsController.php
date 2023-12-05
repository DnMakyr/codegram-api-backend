<?php

namespace App\Http\Controllers\Api;

use App\Notifications\LikeNotification;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\NotificationEvent;

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
            ->paginate(3);

        $suggests = User::whereNotIn('id', $users)
            ->where('id', '!=', auth()->user()->id)
            ->with('profile')
            ->inRandomOrder()
            ->limit(5)
            ->get();

        foreach ($posts as $post) {
            foreach ($post->comments as $comment) {
                // Assuming that the Comment model has a relationship named 'user' for the commenter
                $comment->commenter = $comment->user->username;
            }
            $post->liked = auth()->user()->hasLiked($post); // Assuming this method exists in the package
            $post->likeCount = $post->likersCount(); // Assuming this method exists in the package
            $post->commentCount = $post->comments->count();
        }

        return response()->json([
            'posts' => $posts,
            'suggestions' => $suggests,
        ]);
    }

    public function viewPost(Post $post)
    {
        $post->load(['user' => function ($query) {
            $query->with('profile');
        }, 'comments' => function ($query) {
            $query->with('user.profile');
        }]);
        // 
        $post->liked = auth()->user()->hasLiked($post);
        $post->likeCount = $post->likersCount();
        $post->commentCount = $post->comments->count();
        return response()->json(['post' => $post]);
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
    public function deletePost(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['error' => 'You are not authorized to delete this post']);
        } else {
            $post->delete();
            return response()->json(['success' => 'Post deleted successfully']);
        }
    }

    public function like(Post $post)
    {
        auth()->user()->like($post);
        if (auth()->user()->id !== $post->user_id) {
            $post->user->notify(new LikeNotification(auth()->user(), $post));
            broadcast(new NotificationEvent(auth()->user(), $post, 'like'))->toOthers();
            return response()->json(['success' => 'Post liked successfully']);
        }
    }
    public function unlike(Post $post)
    {
        auth()->user()->unlike($post);
        return response()->json(['success' => 'Post unliked successfully']);
    }
}
