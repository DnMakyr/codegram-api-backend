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

        $postCount = $user->posts->count();
        $followersCount = $user->profile->followers->count();
        $followingCount = $user->following->count();

        return [
            'user' => array_merge($user->toArray(), ['follows' => $follows]),
            'postCount' => $postCount,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount
        ];
    }
    public function update(User $user)
    {
        if (auth()->user()->id === $user->id) {
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
                $imageArray ?? ['image'=>'users-avatar/anon.png'],
            ));
            return response()->json([
                'newAvatar' => $imageArray ?? ['image'=>'users-avatar/anon.png'],
            ], 200);
        } else return response()->json([
            'message' => 'You are not authorized to perform this action'
        ], 403);
    }
}
