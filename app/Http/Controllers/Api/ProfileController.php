<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;


class ProfileController extends Controller
{
    public function show(User $user)
    {
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        $postCount = Cache::remember(
            'count.posts.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->posts->count();
            }
        );
        $followersCount = Cache::remember(
            'count.followers.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->profile->followers->count();
            }
        );
        $followingCount = Cache::remember(
            'count.following.' . $user->id,
            now()->addSeconds(30),
            function () use ($user) {
                return $user->following->count();
            }
        );

        return response()->json([
            'user' => $user,
            'profile' => $user->profile,
            'posts' => $user->posts,
            'follows' => $follows,
            'postCount' => $postCount,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount
        ]);
    }
    public function update(User $user)
    {
        // $this->authorize('update', $user->profile);

        $data = request()->validate(
            [
                'title' => '',
                'description' => '',
                'url' => '',
                'image' => '',
            ]
        );

        if (request('image')) {
            $imagePath = (request('image')->store('profile', 'public'));

            $image = Image::make(public_path("storage/{$imagePath}"));
            $image->save();
            $imageArray = ['image' => $imagePath];
        }

        $user->profile->update(array_merge(
            $data,
            $imageArray ?? [],
        ));

    }
}
