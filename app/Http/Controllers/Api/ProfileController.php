<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friend;
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
        $authUser = auth()->user()->id;
        return response()->json([
            'user' => array_merge($user->toArray(), [
                'follows' => $follows,
                'friendship' => $friendship = Friend::where(function ($query) use ($authUser, $user) {
                    $query->where('requester_id', $authUser)
                        ->where('user_requested_id', $user->id);
                })->orWhere(function ($query) use ($authUser, $user) {
                    $query->where('requester_id', $user->id)
                        ->where('user_requested_id', $authUser);
                })->first() ?? null,
            ]),
            'postCount' => $postCount,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount
        ]);
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
                $imageArray ?? ['image' => 'users-avatar/anon.png'],
            ));
            return response()->json([
                'newAvatar' => '/storage/' . auth()->user()->profile->image,
            ], 200);
        } else return response()->json([
            'message' => 'You are not authorized to perform this action'
        ], 403);
    }
    public function getFriends(User $user)
    {
        $userFriends = $user->friends;
        $friendInfo = [];

        foreach ($userFriends as $userFriend) {
            if ($user->id === $userFriend->requester_id) {
                $friend = User::where('id', $userFriend->user_requested_id)->with('profile')->first();
            } else {
                $friend = User::where('id', $userFriend->requester_id)->with('profile')->first();
            }

            if ($friend) {
                $friendInfo[] = $friend;
            }
        }

        return response()->json([
            'friends' => $friendInfo
        ]);
    }
}
