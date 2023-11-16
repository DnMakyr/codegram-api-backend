<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{

    public function suggest()
    {
        $authUser = auth()->user()->id;

        // Get all users excluding the authenticated user
        $suggests = User::with('profile')->where('id', '!=', $authUser)->get();

        // Iterate through the suggestions and add a 'is_friend' property
        foreach ($suggests as $key => $suggest) {
            $friendship = Friend::where(function ($query) use ($authUser, $suggest) {
                $query->where('requester_id', $authUser)
                    ->where('user_requested_id', $suggest->id);
            })->orWhere(function ($query) use ($authUser, $suggest) {
                $query->where('requester_id', $suggest->id)
                    ->where('user_requested_id', $authUser);
            })->first();

            $suggest->friendship = $friendship ?? null;
            // Remove friends from the suggestions
            if ($suggest->is_friend === 'friend') {
                $suggests->forget($key);
            }
        }

        return $suggests;
    }
    public function add(User $user)
    {
        Friend::create([
            'requester_id' => auth()->user()->id,
            'user_requested_id' => $user->id,
        ]);
        if (!auth()->user()->following->contains($user->profile)) {
            auth()->user()->following()->toggle($user->profile);
        }
    }
    public function cancel(User $user)
    {
        $friendship = Friend::where('requester_id', auth()->user()->id)
            ->where('user_requested_id', $user->id)
            ->first();
        $friendship->delete();
        if (auth()->user()->following->contains($user->profile)) {
            auth()->user()->following()->toggle($user->profile);
        }
    }
    public function accept(User $user)
    {
        $friendship = Friend::where('requester_id', $user->id)
            ->where('user_requested_id', auth()->user()->id)
            ->first();
        $friendship->update([
            'status' => 'friend',
        ]);
        if (!auth()->user()->following->contains($user->profile)) {
            auth()->user()->following()->toggle($user->profile);
        }
    }
    public function decline(User $user)
    {
        $friendship = Friend::where('requester_id', $user->id)
            ->where('user_requested_id', auth()->user()->id)
            ->first();
        $friendship->delete();
    }
    public function unfriend(User $user)
    {
        $authUser = auth()->user()->id;
        $friendship = Friend::where(function ($query) use ($authUser, $user) {
            $query->where('requester_id', $authUser)
                ->where('user_requested_id', $user->id);
        })->orWhere(function ($query) use ($authUser, $user) {
            $query->where('requester_id', $user->id)
                ->where('user_requested_id', $authUser);
        })->first();
        $friendship->delete();
        if (auth()->user()->following->contains($user->profile) && $user->following->contains(auth()->user()->profile)) {
            auth()->user()->following()->toggle($user->profile);
            $user->following()->toggle(auth()->user()->profile);
        }
    }
}
