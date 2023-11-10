<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{

    public function suggest()
    {
        $authUser = auth()->user()->id;
        $suggests = User::with('profile')->where('id', '!=', $authUser)->get();
        return $suggests;
    }
    public function add(User $user)
    {
        auth()->user()->befriend($user);
        // $user->notify(new FriendRequestNotification(auth()->user()));
        if (!auth()->user()->following->contains($user->profile)) {
            auth()->user()->following()->toggle($user->profile);
        }
        // broadcast(new FriendEvent(auth()->user(), $user, 'friend'))->toOthers();
        return redirect()->back();
    }
    public function accept(User $user)
    {
        auth()->user()->acceptFriendRequest($user);
        // broadcast(new FriendEvent(auth()->user(), $user, 'accept'))->toOthers();
        return redirect()->back();
    }
    public function decline(User $user)
    {
        auth()->user()->denyFriendRequest($user);
        return redirect()->back();
    }

    public function unfriend(User $user)
    {
        auth()->user()->unfriend($user);
        if (auth()->user()->following->contains($user->profile) && $user->following->contains(auth()->user()->profile)) {
            auth()->user()->following()->toggle($user->profile);
            $user->following()->toggle(auth()->user()->profile);
        }
        return redirect()->back();
    }
    public function cancel(User $user)
    {
        auth()->user()->unfriend($user);
        if (auth()->user()->following->contains($user->profile)) {
            auth()->user()->following()->toggle($user->profile);
        }
        return redirect()->back();
    }
}
